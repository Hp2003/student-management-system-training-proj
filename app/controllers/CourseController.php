<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/hiren/mvc2/utils/Validator.php');
require_once($root . '/hiren/mvc2/app/models/Course.php');

class CourseController extends Validator
{

    public $id;
    public $name;
    public $created_at;
    public $updated_at;

    /**
     * saves new course in db and return inserted id
     *
     * @return int
     */
    public function save()
    {

        $errors = $this->validate();
        if(count($errors) > 0){
            $_SESSION['add_course_errors'] = $errors;
            return false;
        }

        $course = new Course();
        $course->name = $this->name;
        $result = $course->save();
        if ($result === FALSE) {
            $_SESSION['duplicate_course_error'] = 'The course name is already avaialble';
            $this->set_values_to_session('add_course_form_input_values');
        }
        return $result;
    }

    /**
     * validates all user inputs and return array of errors
     *
     * @return array
     */
    public function validate()
    {
        $errors = [];

        if (empty(trim($this->name))) {
            $errors['name']  = 'Course name is required';
        }

        return $errors;
    }

    /**
     * updates course using id and return true or false
     *
     * @return bool
     */
    public function update()
    {

        $errors = $this->validate();
        if($errors){
            $_SESSION['edit_course_errors'] = $errors;
            return false;
        }

        $is_duplicate = $this->test_duplicate_course(strtoupper($this->name));
        if ($is_duplicate) {
            $_SESSION['update_form_duplicate_course_error'] = 'The course name is already avaialble';
            $this->set_values_to_session('update_course_form_input_values');
            return false;
        }
        $course = new Course();
        $course->find($this->id);
        $course->name = $_POST['name'];
        return $course->update();
    }

    /**
     * call model to delete course from database
     *
     * @return bool
     */
    public function delete()
    {

        $course = new Course();
        $course->find($this->id);
        return $course->delete();
    }

    /**
     * Adds input values to session to show user
     * @param string $name
     *
     * @return void
     */
    public function set_values_to_session($key_name)
    {
        $_SESSION[$key_name] = array(
            'name' => $this->name,
        );
    }

    /**
     * Returns pagination data 
     *
     * @param int $page
     * @param int $limit
     * @param string $column
     * @param string $type
     * 
     * 
     * @return array
     */
    public function paginate($page, $limit, $column = "", $type = "")
    {
        $course = new Course();
        return $course->paginate($page, $limit, $column, $type);
    }
}




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['operation'] === 'edit') {
        $course_controller = new CourseController();
        $course_controller->name = trim($_POST['name']) ?? '';
        $course_controller->id = $_POST['id'] ?? '';
        if ($course_controller->update() === FALSE) {
            header('Location:' .  $_SERVER['HTTP_REFERER']);
        } else {
            $_SESSION['course_message'] = array(
                'type' => 'success',
                'message' => 'Course has been updated',
            );
            header('Location:' .  '/hiren/mvc2/app/views/course');
        }
    } elseif ($_POST['operation'] === 'delete') {
        $course_controller = new CourseController();
        $course_controller->id = $_POST['id'];

        if(!$course_controller->delete()){
            $_SESSION['course_message'] = array(
                'type' => 'danger',
                'message' => 'Failed deleting course',
            );
            header('Location:' .  $_SERVER['HTTP_REFERER']);
        }else{
            $_SESSION['course_message'] = array(
                'type' => 'success',
                'message' => 'Course has been deleted',
            );
            header('Location:' .  $_SERVER['HTTP_REFERER']);
        }

    } else {
        $course_controller = new CourseController();
        $course_controller->name = trim($_POST['name']);
        if ($course_controller->save() === FALSE) {
            header('Location:' .  $_SERVER['HTTP_REFERER']);
        } else {
            $_SESSION['course_message'] = array(
                'type' => 'success',
                'message' => 'Course has been added',
            );
            header('Location:' .  '/hiren/mvc2/app/views/course?page=1');
        }
    }
}
