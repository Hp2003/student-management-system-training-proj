<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$root = $_SERVER['DOCUMENT_ROOT'];
// require_once($root . '/hiren/mvc2/app/Dbconnect.php');
// require_once($root . '/hiren/mvc2/utils/Paginator.php');
require_once(__DIR__ . '/../Dbconnect.php');
require_once(__DIR__ . '/../../utils/Paginator.php');


class Student extends Paginator
{

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $gender;
    public $created_at; // has default value
    public $updated_at; // has default value
    public $course_id;
    public $phone_number;
    protected $table = 'students';

    /**
     * set values of current object to given id returns false if no record found
     *
     * @param int $id
     */
    public function find($id = null)
    {
        if ($id === null || empty($id)) {
            throw new Error('id is not provided');
        }
        // getting student
        $conn = $this->connect();
        $query = "SELECT id, first_name, last_name, email, gender, created_at, updated_at, course_id, phone_number FROM $this->table WHERE id = ?";
        $statement = $conn->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();
        $result = $statement->get_result();
        $conn->close();
        
        if($result->num_rows === 0){
            return false;
        }
        // setting values to current object
        $student = $result->fetch_assoc();
        $this->id = $student['id'];
        $this->first_name = $student['first_name'];
        $this->last_name = $student['last_name'];
        $this->email = $student['email'];
        $this->gender = $student['gender'];
        $this->course_id = $student['course_id'];
        $this->phone_number = $student['phone_number'];

        return true;
    }

    /**
     * makes connection with database
     *
     * @return object
     */
    public function connect()
    {
        $conn = new Dbconnect();
        return $conn->connect();
    }

    /**
     * insert a single new student in db 
     *
     * @return bool
     */
    public function save()
    {

        $course_id = empty($this->course_id) ? NULL : $this->course_id;

        $status = !($course_id === NULL);
        $conn = $this->connect();

        $query = "INSERT INTO $this->table (first_name, last_name, email, gender, course_id, phone_number, status)
        VALUES(?, ?, ?, ?, ?, ?, ?)";
        $statement = $conn->prepare($query);
        $statement->bind_param('ssssisi', $this->first_name, $this->last_name, $this->email, $this->gender, $course_id, $this->phone_number, $status);
        $result = $statement->execute();
        $conn->close();

        return $result;
    }

    /**
     * updates student in db 
     *
     * @return bool
     */
    public function update()
    {

        $course_id = empty($this->course_id) ? NULL : $this->course_id;
        $status = !($course_id === NULL);

        $query = "UPDATE $this->table SET first_name = ?, last_name = ?, email = ?, phone_number = ?, gender = ?, course_id = ?, status = ?  WHERE id = ? ";

        $conn = $this->connect();
        $statement = $conn->prepare($query);
        $statement->bind_param('sssssiii', $this->first_name, $this->last_name, $this->email, $this->phone_number, $this->gender, $course_id, $status, $this->id);
        $result = $statement->execute();
        $conn->close();

        return $result;
    }

    /**
     * returns all students from students table
     *
     * @return array
     */
    public function get()
    {

        $students = [];
        $conn = $this->connect();
        $sql = "SELECT students.*, courses.name AS course_name FROM $this->table LEFT JOIN courses on students.course_id = courses.id";
        $result = $conn->query($sql);
        $conn->close();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }

        return $students;
    }

    /**
     * delets student and return true if deleted false if not
     *
     * @return bool
     */
    public function delete()
    {

        $conn = $this->connect();
        $sql = "DELETE FROM $this->table WHERE id = ? ";
        $statement = $conn->prepare($sql);
        $statement->bind_param('i', $this->id);
        $statement->execute();
        $affected_rows = $conn->affected_rows;
        $conn->close();

        return $affected_rows > 0;
    }

    /**
     * Finds a single student using given column if available return student in array
     * else return empty array throw an error if column name is empty
     *
     * @param string $column
     * @param string $value
     * 
     * 
     * @return array
     */
    public function find_with_column($column, $value)
    {

        if (empty($column)) {
            throw new Error('Column name is required');
            return 0;
        }

        $conn = $this->connect();
        $sql = "SELECT * FROM $this->table WHERE $column = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return [];
    }

    /**
     * Matches if given value is unique for currnet student 
     * throws error if $column or $value is not given
     * id should be provided in constructor param
     * returns bool 
     *
     * @param string $column
     * @param int|string|float $value
     * 
     * 
     * @return bool
     */
    public function check_unique_except($column, $value)
    {

        if (!isset($this->id)) {
            throw new Error('Please privide id in constructor');
        }
        if (empty($column)) {
            throw new Error('Please provide column name');
        }

        $conn = $this->connect();
        $sql = "SELECT id FROM $this->table WHERE $column = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $value, $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();

        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * Sets course to null to all students with given course_id
     *
     * @param int $course_id
     * @return bool
     */
    public function set_course_to_null($course_id)
    {

        $conn = $this->connect();
        $sql = "update $this->table as students join courses on students.course_id = courses.id set students.course_id = null, students.status = 0 where students.course_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $course_id);
        $result = $stmt->execute();
        $conn->close();

        return $result;
    }

    /**
     * returns total number of students associated to a course
     *
     * @param int $course_id
     * 
     * @return int
     */
    public function get_total_students($course_id)
    {

        $conn = $this->connect();
        $sql = "SELECT COUNT(*) AS total FROM $this->table WHERE course_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $conn->close();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['total'];
        }
        return 0;
    }

    /**
     * Returns paginated data
     *
     * @param string $page
     * @param int $limit
     * @param string $column
     * @param string $type
     * 
     * 
     * @return array
     */
    public function paginate($page, $limit, $column, $type)
    {
        return $this->pagination($page, $limit, $column, $type);
    }

}
