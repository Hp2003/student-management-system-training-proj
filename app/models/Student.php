<?php 
require_once('../Dbconnect.php');

class Student extends Dbconnect {
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $gender;
    public $created_at; // has default value
    public $updated_at; // has default value
    public $course_id;
    public $phone_number;
    private $table = 'students';

    /**
     * set values of object to given id (if given)
     *
     * @param int $id
     */
    public function __construct($id = null)
    {
        if($id !== null){

            // getting student
            $conn = $this->connect();
            $query = "SELECT id, first_name, last_name, email, gender, created_at, updated_at, course_id, phone_number FROM $this->table WHERE id = ?";
            $statement = $conn->prepare($query);
            $statement->bind_param('i', $id);
            $statement->execute();
            $result = $statement->get_result();
            $conn->close();
            $student = $result->fetch_assoc(); 

            // setting values to current object
            $this->id = $student['id'];
            $this->first_name = $student['first_name'];
            $this->last_name = $student['last_name'];
            $this->email = $student['email'];
            $this->gender = $student['gender'];
            // $this->created_at = $statement['created_at'];
            // $this->updated_at = $statement['updated_at'];
            $this->course_id = $student['course_id'];
            $this->phone_number = $student['phone_number'];

        }

    }

    /**
     * insert a single new student in db 
     *
     * @return bool
     */
    public function save() {

        $conn = $this->connect();

        $query = "INSERT INTO $this->table (first_name, last_name, email, gender, course_id, phone_number)
        VALUES(?, ?, ?, ?, ?, ?)";
        $statement = $conn->prepare($query);
        $statement->bind_param('ssssis', $this->first_name, $this->last_name, $this->email, $this->gender, $this->course_id, $this->phone_number);
        $statement->execute();
        $conn->close();
        
        return true;

    }

    /**
     * updates student in db 
     *
     * @return bool
     */
    public function update() {

        $query = "UPDATE $this->table SET first_name = ?, last_name = ?, email = ?, phone_number = ?, gender = ?, course_id = ?  WHERE id = ? ";

        $conn = $this->connect();
        $statement = $conn->prepare($query);
        $statement->bind_param('sssssii', $this->first_name, $this->last_name, $this->email, $this->phone_number, $this->gender, $this->course_id, $this->id);
        $result = $statement->execute();    
        $conn->close();

        return $result;

    }

    /**
     * returns all students from students table
     *
     * @return array
     */
    public function get() {

        $students = [];
        $conn = $this->connect();
        $sql = "SELECT * FROM $this->table";
        $result = $conn->query($sql);
        $conn->close();

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }

        return $students;

    }

    /**
     * delets student and return true if deleted 
     *
     * @return bool
     */
    public function delete() {

        $conn = $this->connect();
        $sql = "DELETE FROM $this->table WHERE id = ? ";
        $statement = $conn->prepare($sql);
        $statement->bind_param('i', $this->id);
        $result = $statement->execute();
        $conn->close();

        return $result;
    }
}