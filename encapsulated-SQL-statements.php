<?php
	//封装增删改查语句
class db{
	public $hostname="localhost";
	public $dbname="1603";
	public $tablename="user";
	private $username="root";
	private $password="";
	public $connect;
	public $opts;
	function __construct($tablename=""){
        $this->tablename=empty($tablename)?"demo":$tablename;
        $this->config();
    }
    public function config(){
        $this->connect=new mysqli($this->hostname,$this->username,$this->password,$this->dbname);//连接数据库
        if(mysqli_connect_errno($this->connect)){
            echo "连接数据库失败";
            $this->connect->close();
            exit;
        }
        $this->connect->query("set names utf8");//查询语言
        $this->opts["filed"]=$this->opts["filed"]?$this->opts["filed"]:"*";
        $this->opts["where"]=$this->opts["order"]=$this->opts["limit"]=" ";
        $this->opts["keys"]=$this->opts["values"]="";
    }
    //查
	public function select($sql=""){
        $sql=empty($sql)?"select ".$this->opts["filed"]." from ".$this->tablename." ".$this->opts["where"]." ".$this->opts["order"]." ".$this->opts["limit"]:$sql;
        $result=$this->connect->query($sql);
        $arr=array();
        while($row=$result->fetch_assoc()){
            $arr[]=$row;
        };
        return $arr;
    }
    // 增
    public function insert($sql=""){
        	$this->opts["filed"]="(".$this->opts["keys"].") values (".$this->opts["values"].")";
            $sql=empty($sql)?"insert into ".$this->tablename." ".$this->opts["filed"]:$sql;
            $this->connect->query($sql);
            return $this->connect->affected_rows;

    }
    // 删
    public function delete($sql=""){
        $sql=empty($sql)?"delete from ".$this->tablename." ".$this->opts["where"]:$sql;
        $this->connect->query($sql);
        return $this->connect->affected_rows;
    }
    // 改
    public function update($sql=""){
        $sql=empty($sql)?"update ".$this->tablename." set ".$this->opts["filed"]." ".$this->opts["where"]:$sql;
        $this->connect->query($sql);
        return $this->connect->affected_rows;
    }
    // 字段
	public function filed($sql=""){
        $sql=empty($sql)?"*":$sql;
        $keys="";
        $values="";
        if(strpos($sql,";")){
            $arr=explode(";",$sql);
            foreach ($arr as $k=>$v){
                $newarr=explode("=",$v);
                $keys.=$newarr[0].",";
                $values.=$newarr[1].",";
            }
            $sql=str_replace(";",",",$sql);
			$this->opts["keys"]=substr($keys,0,-1);
			$this->opts["values"]=substr($values,0,-1);
        }else{
            $arr2=explode("=",$sql);
			$this->opts["keys"]=$arr2[0];
			$this->opts["values"]=$arr2[1];
        }
        $this->opts["filed"]=$sql;
        return $this;
    }
    // 条件
    public function where($sql=""){
        $sql=empty($sql)?"":"where ".$sql;
        $this->opts["where"]=$sql;
        return $this;
    }
    // 排序
    public function order($sql=""){
        $sql=empty($sql)?"":"ORDER BY ".$sql;
        $this->opts["order"]=$sql;
        return $this;
    }
    // 截取
    public function limit($sql=""){
        $sql=empty($sql)?"":"limit ".$sql;
        $this->opts["limit"]=$sql;
        return $this;
    }
}
$db=new db("user");
var_dump($db->filed("username='888'")->where("uid=20")->update());


//面向对象的方式写增删改查语句
//	$db->filed("name")->where("id=3")->select();
//	$db->filed("name=zhangsan,age=12")->where("id=3")->update();
//	$db->filed("name=zhangsan")->insert();
//	$db->filed("name")->del();
?>