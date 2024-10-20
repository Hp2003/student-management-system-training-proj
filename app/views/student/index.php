<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/hiren/mvc2/app/models/Student.php');
require_once($root . '/hiren/mvc2/app/models/Course.php');
require_once($root . '/hiren/mvc2/app/controllers/StudentController.php');

$navbar = include_once('../nav.php');

// Pattern to formate phone number
$pattern = "/^(\d{3})(\d{3})(\d{4})$/";

$sort_by = !empty($_GET['sort_by']) ? $_GET['sort_by'] : "";
$type = !empty($_GET['type']) ? $_GET['type'] : "";
$limit = $_GET['limit'] ?? 5;
$page = !empty($_GET['page']) ? $_GET['page'] : 1;

// Getting paginated students data
$student_controller = new StudentController();
$pagination_data = $student_controller->paginate($page, $limit, $sort_by, $type);
$pages = [];
$students = [];

if($pagination_data !== false){
  $pages = $pagination_data['pagination_numbers'] ?? 0;
  
  if($page > $pages['total_pages'] && $page > 1){
    header("Location: /hiren/mvc2/app/views/student?limit=$limit&type=$type&sort_by=$sort_by&page=" . $pages['total_pages']);
  }
  unset($pagination_data['pagination_numbers']);
  $students = $pagination_data;
}else{
  $courses = [];
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bootstrap demo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <?php $navbar ?>
  <?php if (!empty($_SESSION['student_message'])) {
    $alert = $_SESSION['student_message'];
  ?>
    <div class="alert alert-<?php echo $alert['type'] ?> alert-dismissible fade show" role="alert">
      <strong></strong> <?php echo $alert['message'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php } ?>
  <div class=" mx-auto mt-5" style="width : 90%;">
    <?php
    if (count($students) <= 0) {
    ?>
      <h1 class="text-center mt-5"> No records found :( </h1>
    <?php
    } else {
    ?>
      <div class="container d-flex justify-content-around">
        <form action="/hiren/mvc2/app/views/student" class="w-25 limit-form d-flex ">
          <?php /*<input type="hidden" name="sort_by" value=<?php echo $sort_by ?>>
          <input type="hidden" name="type" value=<?php echo $type ?>> */ ?>
          <select class="form-select limit" aria-label="Default select example" name="limit">
            <option value="5" <?php echo $limit == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
            <option value="20" <?php echo $limit == 20 ? 'selected' : '' ?>>20</option>
            <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
          </select>
          <input type="submit" value="Filter" class="btn btn-primary">
        </form>
        <form action="../../controllers/StudentController.php" method="post">
          <input type="hidden" name="operation" value="csv">
          <button type="submit" class="btn btn-primary">Download</button>
        </form>
      </div>
      <table class="table table-striped">
        <thead>
          <tr class="user-select-none">
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="id">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p class="mx-2">id</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="first_name">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>first_name</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="last_name">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>last_name</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="email">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>email</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="gender">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>gender</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="course_id">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>course</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="status">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>status</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="phone_number">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>phone</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="created_at">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>created_at</p>
            </th>
            <th scope="col " class="heading">
              <form action="/hiren/mvc2/app/views/student/">
                <input type="hidden" name="sort_by" value="updated_at">
                <?php /*<input type="hidden" name="page" value="<?php echo $page ?>"> */?>
                <input type="hidden" name="limit" value="<?php echo $limit ?>">
                <button class="" value="ASC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2191;</button>
                <button class="" value="DESC" name="type" style="font-size: 2rem; padding : 0; margin : 0; ">&#x2193;</button>
              </form>
              <p>updated_at</p>
            </th>
            <th scope="col "></th>
            <th scope="col "></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $student) { ?>
            <tr>
              <th scope="row"><?php echo $student['id'] ?></th>
              <td><?php echo $student['first_name'] ?></td>
              <td><?php echo $student['last_name'] ?></td>
              <td><?php echo $student['email'] ?></td>
              <td><?php echo $student['gender'] ?></td>
              <td><?php echo $student['course_name'] ?? 'N/A' ?></td>
              <td><?php echo $student['status'] ? 'Active' : 'Inactive' ?></td>
              <td><?php echo preg_replace($pattern, '$1-$2-$3', $student['phone_number']) ?></td>
              <td><?php echo date('d-m-Y h : i a', strtotime($student['created_at'])) ?></td>
              <td><?php echo date('d-m-Y h : i a', strtotime($student['updated_at'])) ?></td>
              <td><a type="button" href="<?php echo "/hiren/mvc2/app/views/student/editStudent.php?id=" . $student['id']  ?>" class="btn btn-primary">Edit</a></td>
              <td>
                <form action="../../controllers/StudentController.php" method="POST">
                  <input type="hidden" name="id" value="<?php echo $student['id'] ?>">
                  <input type="hidden" name="operation" value="delete">
                  <button type="submit" class="btn btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
  </div>

  <?php require_once('../paginator.php') ?>
<?php } ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>

<?php if(!empty($pages) && $pages['page'] <= $pages['total_pages']) unset($_SESSION['student_message']) ?>