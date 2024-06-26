<?php
// Tạo header cho việc kiểm soát đầy đủ
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Thông tin server
$server = "localhost";
$username = "root";
$password = "";
$database = "qlnh_perfact";

// Kết nối
$conn = new mysqli($server, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

$Username = isset($_GET['Username']) ? $_GET['Username'] : '';
$Password = isset($_GET['Password']) ? $_GET['Password'] : '';

$DMFoodID = isset($_GET['DMFoodID']) ? $_GET['DMFoodID'] : '';

$FoodID = isset($_GET['FoodID']) ? $_GET['FoodID'] : '';

$FoodName = isset($_GET['FoodName']) ? $_GET['FoodName'] : '';
$Quantity = isset($_GET['Quantity']) ? $_GET['Quantity'] : '';
$Price = isset($_GET['Price']) ? $_GET['Price'] : '';
$TableName = isset($_GET['TableName']) ? $_GET['TableName'] : '';
$BillDate = isset($_GET['BillDate']) ? $_GET['BillDate'] : '';
$TotalAmount = isset($_GET['TotalAmount']) ? $_GET['TotalAmount'] : '';
$CustomerName = isset($_GET['CustomerName']) ? $_GET['CustomerName'] : '';
$SDT = isset($_GET['SDT']) ? $_GET['SDT'] : '';

switch($action){
    case "checkAccount":
        checkAccount($conn, $Username, $Password);
        break;
    case "home":
        home($conn);
        break;
    case "getTable":
        getTable($conn);
        break;
    case "getDmFood":
        getDmFood($conn, $TableName);
        break;
    case "getFood":
        getFood($conn, $DMFoodID);
        break;
    case "addBill":
        addBill($conn, $FoodName, $FoodID, $Quantity, $TableName, $BillDate, $CustomerName, $SDT);
        break;
    case "getDatalBillCurrent":
        getDatalBillCurrent($conn, $TableName);
        break;
    case "plushQuantityItem":
        plushQuantityItem($conn, $FoodName, $TableName, $CustomerName, $SDT);
        break;
    default:
        echo "Không xác định";
}

function checkAccount($conn, $Username, $Password) {
    $sql = "SELECT * FROM tbaccount where Username like '$Username' and Password like '$Password'";
   
    $result = $conn->query($sql);
    
    //Tạo mảng trống
    $rows = array();
    
    while ($row = $result->fetch_assoc()){
        $rows[] = $row;
    }

    $json = json_encode(array("taikhoan" => $rows), JSON_PRETTY_PRINT);

    file_put_contents('data.json', $json);
}

function home($conn){
    $sql_totalamount = "SELECT SUM(TotalAmount) AS TotalAmount FROM tbBillHistory WHERE DATE(BillDate) = CURDATE()";

    $result_totalamount = $conn->query($sql_totalamount);

    $rows_totalamount = array();
    
    while ($row_totalamount = $result_totalamount->fetch_assoc()){
        $rows_totalamount[] = $row_totalamount;
    }

    $json = json_encode(array("totalAmount" => $rows_totalamount), JSON_PRETTY_PRINT);

    file_put_contents('data.json', $json);
}

function getTable($conn){
    $sql_table = "SELECT * FROM tbdstable";

    $result_table = $conn->query($sql_table);

    $rows_table = array();
    
    while ($row_table = $result_table->fetch_assoc()){
        $rows_table[] = $row_table;
    }

    $json = json_encode(array("ListTable" => $rows_table), JSON_PRETTY_PRINT);

    file_put_contents('data.json', $json);
}

function getDmFood($conn, $TableName){
    $sql_dmFood = "SELECT * FROM tbdmfood";
    $sql_Food = "SELECT * FROM tbfood WHERE DMFoodID = 1";
    $sql_bill = "SELECT * FROM tbbilldetails WHERE TableName = '$TableName'";

    $result_dmFood = $conn->query($sql_dmFood);
    $result_Food = $conn->query($sql_Food);
    $result_bill = $conn->query($sql_bill);

    $rows_dmFood = array();
    $rows_Food = array();

    while ($row_dmFood = $result_dmFood->fetch_assoc()){
        $rows_dmFood[] = $row_dmFood;
    }

    while ($row_Food = $result_Food->fetch_assoc()){
        $rows_Food[] = $row_Food;
    }

    if($result_bill && $result_bill->num_rows > 0){
        $rows_bill = array();
        while ($row_bill = $result_bill->fetch_assoc()){
            $rows_bill[] = $row_bill;
        }
        $json = json_encode(array("ListCategory" => $rows_dmFood,"ListFood" => $rows_Food, "BillCurrentOfTable" => $rows_bill), JSON_PRETTY_PRINT);
        
        file_put_contents('data.json', $json);
    }else{
        $json = json_encode(array("ListCategory" => $rows_dmFood,"ListFood" => $rows_Food), JSON_PRETTY_PRINT);

        file_put_contents('data.json', $json);
    }
}

function getFood($conn, $DMFoodID){
    $sql_Food = "SELECT * FROM tbfood WHERE DMFoodID = '$DMFoodID'";

    $result_Food = $conn->query($sql_Food);

    $rows_Food = array();
    
    while ($row_Food = $result_Food->fetch_assoc()){
        $rows_Food[] = $row_Food;
    }

    $json = json_encode(array("ListFood" => $rows_Food), JSON_PRETTY_PRINT);

    file_put_contents('data.json', $json);
}

function addBill($conn, $FoodName, $FoodID, $Quantity, $TableName, $BillDate, $CustomerName, $SDT){
    $sql_searchFoodName = "SELECT * FROM tbfood WHERE FoodID = '$FoodID'";
    $result_searchFoodName = $conn->query($sql_searchFoodName);

    if ($result_searchFoodName && $result_searchFoodName->num_rows > 0) {

        $row_searchFoodName = $result_searchFoodName->fetch_assoc();
        $FoodName = $row_searchFoodName['FoodName'];
        $Price = $row_searchFoodName['Price'];

        //check tồn tại của food trước đó
        $sql_check = "SELECT * FROM tbBillDetails WHERE FoodName = '$FoodName' and TableName='$TableName'";
        $result_check = $conn->query($sql_check);

        if($result_check && $result_check->num_rows > 0){

            $row_check = $result_check->fetch_assoc();

            $Quantity = $row_check['Quantity'];
            $QuantityCurrent = $Quantity + 1;

            $TotalAmount = $Price * $QuantityCurrent;

            $sql_addBill = "UPDATE tbBillDetails SET TotalAmount = '$TotalAmount', Quantity='$QuantityCurrent' WHERE FoodName = '$FoodName'";
            
            $result_addBill = $conn->query($sql_addBill);
        }else if($result_check->num_rows == 0){
            $sql_addBill = "INSERT INTO tbBillDetails (FoodName,TableName,BillDate,Price,CustomerName,SDT,Quantity,TotalAmount) VALUES ('$FoodName','$TableName','$BillDate','$Price','$CustomerName','$SDT',1,'$Price')";
            
            $result_addBill = $conn->query($sql_addBill);
        }
    } 
}

function plushQuantityItem($conn, $FoodName, $TableName, $CustomerName, $SDT){
    //check tồn tại của food trước đó
    $sql = "SELECT * FROM tbBillDetails WHERE FoodName = '$FoodName' and TableName='$TableName' and CustomerName = '$CustomerName' and SDT = '$SDT'";
    $result = $conn->query($sql);

    if($result && $result->num_rows > 0){

        $row = $result->fetch_assoc();

        $Quantity = $row['Quantity'];
        $Price = $row['Price'];
        $QuantityCurrent = $Quantity + 1;

        $TotalAmount = $Price * $QuantityCurrent;

        $sql_plush = "UPDATE tbBillDetails SET TotalAmount = '$TotalAmount', Quantity='$QuantityCurrent' WHERE FoodName = '$FoodName' and TableName='$TableName' and CustomerName = '$CustomerName' and SDT = '$SDT'";
        
        $result_plush = $conn->query($sql_plush);
    }
}

function getDatalBillCurrent($conn, $TableName){
    $sql = "SELECT * FROM tbBillDetails WHERE TableName='$TableName'";

    $result = $conn->query($sql);

    $rows = array();

    while ($row = $result->fetch_assoc()){
        $rows[] = $row;
    }

    $json = json_encode(array("BillCurrentOfTable" => $rows), JSON_PRETTY_PRINT);

    file_put_contents('data.json', $json);
}
$conn->close();
?>