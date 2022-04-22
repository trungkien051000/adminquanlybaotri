<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="")
    {   
    header("Location: index.php"); 
    }
    else{
if(isset($_POST['submit']))
{
    $marks=array();
$class=$_POST['class'];
$studentid=$_POST['studentid']; 
$mark=$_POST['marks'];

 $stmt = $dbh->prepare("SELECT tblsubjects.SubjectName,tblsubjects.id FROM tblsubjectcombination join  tblsubjects on  tblsubjects.id=tblsubjectcombination.SubjectId WHERE tblsubjectcombination.ClassId=:cid order by tblsubjects.SubjectName");
 $stmt->execute(array(':cid' => $class));
  $sid1=array();
 while($row=$stmt->fetch(PDO::FETCH_ASSOC))
 {

array_push($sid1,$row['id']);
   } 
  
for($i=0;$i<count($mark);$i++){
    $mar=$mark[$i];
  $sid=$sid1[$i];
$sql="INSERT INTO  tblresult(StudentId,ClassId,SubjectId,marks) VALUES(:studentid,:class,:sid,:marks)";
$query = $dbh->prepare($sql);
$query->bindParam(':studentid',$studentid,PDO::PARAM_STR);
$query->bindParam(':class',$class,PDO::PARAM_STR);
$query->bindParam(':sid',$sid,PDO::PARAM_STR);
$query->bindParam(':marks',$mar,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Result info added successfully";
}
else 
{
$error="Something went wrong. Please try again";
}
}
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin | Thống kê </title>
        <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" >
        <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
        <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen" >
        <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen" >
        <link rel="stylesheet" href="css/prism/prism.css" media="screen" >
        <link rel="stylesheet" href="css/select2/select2.min.css" >
        <link rel="stylesheet" href="css/main.css" media="screen" >
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
        <script src="js/modernizr/modernizr.min.js"></script>

    </head>
    <body class="top-navbar-fixed">
        <div class="main-wrapper">

            <!-- ========== TOP NAVBAR ========== -->
  <?php include('includes/topbar.php');?> 
            <!-- ========== WRAPPER FOR BOTH SIDEBARS & MAIN CONTENT ========== -->
            <div class="content-wrapper">
                <div class="content-container">

                    <!-- ========== LEFT SIDEBAR ========== -->
                   <?php include('includes/leftbar.php');?>  
                    <!-- /.left-sidebar -->

                    <div class="main-page">

                     <div class="container-fluid">
                            <div class="row page-title-div">
                                <div class="col-md-6">
                                    <h2 class="title">Thống kê</h2>
                                
                                </div>
                                
                                <!-- /.col-md-6 text-right -->
                            </div>
                            <!-- /.row -->
                            <div class="row breadcrumb-div">
                                <div class="col-md-6">
                                    <ul class="breadcrumb">
                                        <li><a href="dashboard.php"><i class="fa fa-home"></i> Trang chủ</a></li>
                                
                                        <li class="active">Thống kê</li>
                                    </ul>
                                </div>
                             
                            </div>
                            <!-- /.row -->
                        </div>
                        <div class="container-fluid">
                           
                        <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel">
                                           
                                            <div class="panel-body">

    <form class="form-horizontal" method="post" >
    <div class="row">
      <div class="col-sm-6">
        <canvas id="canvas"></canvas>
      </div>
      <div class="col-sm-6">
        <canvas id="SLBT"></canvas>
      </div>
    </div>
    
 
      </form>
      <script>
      let myChart = document.getElementById('canvas').getContext('2d');
      // Global Options
      // Chart.defaults.global.defaultFontFamily = 'Lato';
      Chart.defaults.global.defaultFontSize = 18;
      Chart.defaults.global.defaultFontColor = '#111';

      let massPopChart = new Chart(myChart, {
        type:'pie', //loại biểu đồ: bar, horizontalBar, pie, line, doughnut, radar, polarArea
        data:{
          labels:[
          <?php
            include('includes/ketnoi.php');
            $data = '';
            $tv="select * from trangthai";
            $tv_1=mysqli_query($connect,$tv);
            while($tv_2=mysqli_fetch_array($tv_1))
            {
              if($tv_2!=false)
              {
                echo "'".$tv_2['TrangThai']."',";
                //lấy data
                $tvc="select COUNT(MaBaoTri) as status from chitietbaotri where MaTrangThai = '". $tv_2['MaTrangThai'] ."'";
                $tvc_1=mysqli_query($connect,$tvc);
                while($tvc_2=mysqli_fetch_array($tvc_1))
                {
                  if($tvc_2!=false)
                  {
                    $data = $data.$tvc_2['status'].",";
                  }
                }
              }
            }
          ?>
          ],
          datasets:[{
            label:'Population',
            data:[
              <?php echo $data; ?>
            ],
            //backgroundColor:'green',
            backgroundColor:[
              'rgba(255, 99, 132)',
              'rgba(54, 162, 235)',
              'rgba(153, 102, 255)',
              'rgba(255, 159, 64)',
              'rgba(39,123,74)',
              'rgba(16,0,103)',
              'rgba(204,0,0)'
            ],
            borderWidth:1,
            borderColor:'#777',
            hoverBorderWidth:3,
            hoverBorderColor:'#000'
          }]
        },
        options:{
          title:{
            display:true,
            text:'Biểu đồ trạng thái bảo trì',
            fontSize:25
          },
          legend:{
            display:true,
            position:'right',
            labels:{
              fontColor:'#000'
            }
          },
          layout:{
            padding:{
              left:50,
              right:0,
              bottom:0,
              top:0
            }
          },
          tooltips:{
            enabled:true
          }
        }
      });
    </script>
    <script>
      let SLBT = document.getElementById('SLBT').getContext('2d');
      // Global Options
      // Chart.defaults.global.defaultFontFamily = 'Lato';
      Chart.defaults.global.defaultFontSize = 18;
      Chart.defaults.global.defaultFontColor = '#111';

      let SLBT1 = new Chart(SLBT, {
        type:'bar', //loại biểu đồ: bar, horizontalBar, pie, line, doughnut, radar, polarArea
        data:{
          labels:[
          <?php
            include('includes/ketnoi.php');
            $data = '';
            $tv="select * from thanhpho";
            $tv_1=mysqli_query($connect,$tv);
            while($tv_2=mysqli_fetch_array($tv_1))
            {
              if($tv_2!=false)
              {
                echo "'".$tv_2['TenTP']."',";
                //lấy data
                $tvc="SELECT COUNT(baotri.MaBaoTri) as sl 
                from baotri
                left join khachhang on khachhang.MaKhachHang =baotri.MaKhachHang 
                left join diachi on diachi.MaDiaChi = khachhang.MaDiaChi 
                left join phuong on diachi.MaPhuong = phuong.MaPhuong
                left join quan on phuong.MaQuan = quan.MaQuan
                left join thanhpho on quan.MaTP = thanhpho.MaTP
                WHERE  thanhpho.TenTP ='". $tv_2['TenTP'] ."'";
                $tvc_1=mysqli_query($connect,$tvc);
                while($tvc_2=mysqli_fetch_array($tvc_1))
                {
                  if($tvc_2!=false)
                  {
                    $data = $data.$tvc_2['sl'].",";
                  }
                }
              }
            }
          ?>
          ],
          datasets:[{
            label:'Số lượng bảo trì',
            data:[
              <?php echo $data; ?>
            0,],
            backgroundColor:[
            ],
            borderWidth:1,
            borderColor:'#777',
            hoverBorderWidth:3,
            hoverBorderColor:'#000'
          }]
        },
        options:{
          title:{
            display:true,
            text:'Biểu đồ số lượng bảo trì các thành phố',
            fontSize:25
          },
          legend:{
            display:true,
            position:'right',
            labels:{
              fontColor:'#000'
            }
          },
          layout:{
            padding:{
              left:50,
              right:0,
              bottom:0,
              top:0
            }
          },
          tooltips:{
            enabled:true
          }
        }
      });
    </script>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.col-md-12 -->
                                </div>
                    </div>
                </div>
                <!-- /.content-container -->
            </div>
            <!-- /.content-wrapper -->
        </div>
        <!-- /.main-wrapper -->
        <script src="js/jquery/jquery-2.2.4.min.js"></script>
        <script src="js/bootstrap/bootstrap.min.js"></script>
        <script src="js/pace/pace.min.js"></script>
        <script src="js/lobipanel/lobipanel.min.js"></script>
        <script src="js/iscroll/iscroll.js"></script>
        <script src="js/prism/prism.js"></script>
        <script src="js/select2/select2.min.js"></script>
        <script src="js/main.js"></script>
        <script>
            $(function($) {
                $(".js-states").select2();
                $(".js-states-limit").select2({
                    maximumSelectionLength: 2
                });
                $(".js-states-hide").select2({
                    minimumResultsForSearch: Infinity
                });
            });
        </script>
    </body>
</html>
<?PHP } ?>
