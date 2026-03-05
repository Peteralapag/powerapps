<?php include '../../init.php'; ?>
<style>
.dashboard {display: flex;background: url('../Images/media/<?php echo BG_WALLPAPER; ?>') no-repeat;background-size: cover;background-position: center;width:100%;height:100%;padding:20px;}
.notifs {display:none;width: 250px;max-height 100vh;border:5px solid #fff;;margin-left: auto;border-radius: 7px;overflow: hidden;background-color: rgb(0,0,0); /* Fallback color */background-color: rgba(255,255,255, 0.77); /* Black w/opacity/see-through */}
.notifs-title {padding: 8px;text-align:center;background:#FFF;font-size:14PX;letter-spacing:5px;border-bottom:3px solid #f1f1f1;}
.notif-data {padding:5px;font-size:12px;overflow:hidden;overflow:auto;}
.xmark {position:absolute;right:5px;top: 5px;font-size:18px;cursor: pointer;}
.xmark:hover {color:red;}
</style>
<div class="dashboard" id="dashboarddata"></div>
