<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $root = $_SERVER['DOCUMENT_ROOT'];
    require_once($root . '/hiren/mvc2/app/models/Course.php');
    $navbar = include_once('../nav.php');
    
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    if(empty($_GET['id'])){
        header('Location:' . '/hiren/mvc2/app/views/404.php');
    }
    $course = new Course();

    if(!$course->find($_GET['id'])){
        header('Location:' . '/hiren/mvc2/app/views/404.php');
    }

    $duplicate_course_error = $_SESSION['update_form_duplicate_course_error'] ?? '';
    $error_value = $_SESSION['update_course_form_input_values'] ?? [];

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <?php $navbar ?>
    <div class="container mt-5 w-25">
        <h1>Edit Course</h1>
        <form action="../../controllers/CourseController.php" method="post">
            <div class="mb-3">
                <input type="hidden" name="operation" value="edit">
                <input type="hidden" name="id" value="<?php echo $course->id ?>">
                <label for="exampleInputEmail1" class="form-label" >Course Name : </label>
                <input type="text" name="name" class="form-control" value="<?php echo $error_value['name'] ?? $course->name  ?>"  id="" required>
                <span class="text-danger"><?php echo $duplicate_course_error ?></span>
                <span class="text-danger"><?php echo $_SESSION['edit_course_errors']['name'] ?? '' ?></span>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Course</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
<?php 
    unset($_SESSION['update_form_duplicate_course_error']);
    unset($_SESSION['update_course_form_input_values']);
    unset($_SESSION['edit_course_errors']);
?>