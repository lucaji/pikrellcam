<script>
var servo_mode = 0;

var servo_left_array = [
    "images/arrow0-left.png",
    "images/arrow-left.png",
    "images/arrow2-left.png"
    ];

var servo_right_array = [
    "images/arrow0-right.png",
    "images/arrow-right.png",
    "images/arrow2-right.png"
    ];

var servo_up_array = [
    "images/arrow0-up.png",
    "images/arrow-up.png",
    "images/arrow2-up.png"
    ];

var servo_down_array = [
    "images/arrow0-down.png",
    "images/arrow-down.png",
    "images/arrow2-down.png"
    ];

function servo_move_mode() {
    servo_mode += 1;
    if (servo_mode > servo_left_array.length - 1) {
        servo_mode = 0;
    }
    document.getElementById("servo_left").src = servo_left_array[servo_mode];
    document.getElementById("servo_right").src = servo_right_array[servo_mode];
    document.getElementById("servo_up").src = servo_up_array[servo_mode];
    document.getElementById("servo_down").src = servo_down_array[servo_mode];
}

function servo_move_command(pan_tilt) {
//  alert("motion " + move_mode +  " " + where);
    fifo_command("servo " +  pan_tilt +  " " + servo_mode);
}

</script>

<?php
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);


    require_once(dirname(__FILE__) . '/config.php');
    include_once(dirname(__FILE__) . '/config-user.php');
    include_once(dirname(__FILE__) . '/config-defaults.php');   

function time_lapse_period() {
    $tl_status = "../../.pikrellcam/timelapse.status";
    $f = fopen($tl_status, 'r');
    $tl_period = 1;
    if ($f) {
        $input = fgets($f);
        $input = fgets($f);
        sscanf($input, "%d", $tl_period);
        fclose($f);
    }
    return $tl_period;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo TITLE_STRING; ?></title>
  <link rel="stylesheet" href="js-css/bootstrap.min.css" />
  <link rel="stylesheet" href="js-css/docs.css" />
  <link rel="stylesheet" href="js-css/bootstrap-icons.css" />
  <script src="js-css/jquery-3.6.1.min.js"></script>
  <script src="js-css/pikrellcam.js"></script>
</head>

<?php
    if (isset($_GET["hide_audio"])) {
        $show_audio_controls = "no";
        config_user_save();
    }
    if (isset($_GET["show_audio"])) {
        $show_audio_controls = "yes";
        config_user_save();
    }
    
    if (defined('SERVOS_ENABLE'))
        $servos_enable = SERVOS_ENABLE;
    else
        $servos_enable = "servos_off";
?>

<body onload="mjpeg_start();">

   <div class="container text-center">
      <div class="text-shadow-large"><?php echo TITLE_STRING; ?></div>
   </div>
   <div class="container text-center">
      <img id="mjpeg_image"
      alt="No preview jpeg. Is pikrellcam running?  Click: System->Start"
      style="margin:6px;"
      onclick="image_expand_toggle();">
   </div>

   <div class="container text-center top-margin">

<?php
if (defined('SHOW_AUDIO_CONTROLS')) {
    if ($show_audio_controls == "yes") {
    ?>
        <audio id="audio_fifo" controls src="audio_stream.php"
              hidden="hidden" preload="none" type="audio/mpeg">
              MP3 not supported </audio>
              
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="audio_stop()">
      <i class="bi bi-volume-mute"></i>
      </button>
              
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="audio_play()">
      <i class="bi bi-voicemail"></i>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('audio mic_toggle')">
      <i class="bi bi-mic-mute-fill"></i>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('audio gain up')">
      <i class="bi bi-volume-up"></i>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('audio gain down')">
      <i class="bi bi-volume-down"></i>
      </button>
      
 <?php
        }
    }
?>
      <a href="logger/">
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" title="Logger">
      <span class="bi bi-graph-down"></span> Logger
      </button>
      </a>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('record off')">
      <i class="bi bi-stop-circle"></i>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('pause')">
      <i class="bi bi-pause-circle"></i>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('record on')">
      <i class="bi bi-record-circle"></i>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('still')">
      <i class="bi bi-camera"></i>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('loop toggle')">
      <i class="bi bi-repeat"></i>
      </button>
      

      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('preset next_settings')">
      <span class="bi bi-arrow-up-circle">Next Preset</span>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('preset prev_settings')">
      <span class="bi bi-arrow-down-circle">Prev Preset</span>
      </button>
      
      <button class="ui-button ui-widget ui-corner-all ui-button-icon-only" onclick="fifo_command('motion_enable toggle')">
      <span class="bi bi-motherboard-fill">Motion</span>
      </button>
      
<?php
if ($servos_enable == "servos_on") {
    echo "<input type='image' id='preset_left' src='images/arrow-left.png'
           class=\"ui-button ui-widget ui-corner-all\"
           style='margin-left:2px; vertical-align: bottom;'
           onclick=\"fifo_command('preset prev_position')\">";
    echo "<input type='image' id='preset_right' src='images/arrow-right.png'
           class=\"ui-button ui-widget ui-corner-all\"
           style='margin-left:2px; vertical-align: bottom;'
           onclick=\"fifo_command('preset next_position')\">";
    echo "<input id='servo_move_mode' type='button' value=\"Servo:\"
            class=\"btn-control\"
            style=\"cursor: pointer;
            background: rgba(0, 0, 0, 0.08);
            color: $default_text_color; margin-left:20px; padding-left:2px; padding-right:0px;\"
            onclick='servo_move_mode();'>";
    echo "<input type='image' id='servo_left' src='images/arrow0-left.png'
            style='margin-left:2px; vertical-align: bottom;'
            onclick=\"servo_move_command('pan_left')\">";
    echo "<input type='image' id='servo_right' src='images/arrow0-right.png'
            style='margin-left:2px; vertical-align: bottom;'
            onclick=\"servo_move_command('pan_right')\">";
    echo "<input type='image' id='servo_up' src='images/arrow0-up.png'
            style='margin-left:2px; vertical-align: bottom;'
            onclick=\"servo_move_command('tilt_up')\">";
    echo "<input type='image' id='servo_down' src='images/arrow0-down.png'
            style='margin-left:2px; vertical-align: bottom;'
            onclick=\"servo_move_command('tilt_down')\">";
}

if (defined('INCLUDE_CONTROL')) {
    if ($include_control == "yes") {
        include 'control.php';
    }
}

if (file_exists("custom-control.php")) {
    include 'custom-control.php';
}

?>

</div>

<div class="container top-margin">

<?php

$archive_root = ARCHIVE_DIR;
$fs_type = exec("stat -f -L -c %T $archive_root");
if ("$fs_type" == "nfs")
    $arch_type = "NFS";
else if (strpos($fs_type, 'Stale') !== false)
    $arch_type = "Stale";
else
    $arch_type = "";

echo "<a href=\"archive.php\"
    class=\"ui-button ui-widget ui-corner-all\"
        style='margin-right:20px;'>
        $arch_type Archive Calendar</a>";

echo "<span style=\"color: $default_text_color\"> Media:</span>";
echo "<a href='media-archive.php?mode=media&type=videos'
        class=\"ui-button ui-widget ui-corner-all\"
        style='margin-left:2px;'
        >Videos</a>";
echo "<a href='media-archive.php?mode=media&type=stills'
        class=\"ui-button ui-widget ui-corner-all\"
        style='margin-left:2px; margin-right:8px;'
        >Stills</a>";
echo "<a href='media-archive.php?mode=loop&type=videos'
        class=\"ui-button ui-widget ui-corner-all\"
        style='margin-left:2px; margin-right:30px;'
        >Loop</a>";
echo "<span style=\"color: $default_text_color\"> Enable:</span>";
?>

      <input type="button" id="motion_button" value="Motion"
         onclick="fifo_command('motion_enable toggle')"
         class="ui-button ui-widget ui-corner-all"
      >
      
      <span style="float: right;"> Show:
      
        <input type="button" id="regions_button" value="Preset"
                        onclick="fifo_command('motion show_regions toggle')"
                        class="ui-button ui-widget ui-corner-all"
                        >
        <input id="timelapse_button" type="button" value="Timelapse"
                        onclick="fifo_command('tl_show_status toggle')"
                        class="ui-button ui-widget ui-corner-all"
                        >
        <input type="button" id="vectors_button" value="Vectors"
                        onclick="fifo_command('motion show_vectors toggle')"
                        class="ui-button ui-widget ui-corner-all"
                        >
      </span>
      
    </div>

<div class="container">
  <div class="accordion" id="accordionExample">
    <div class="accordion-item">
      <h3 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
        SETUP
      </button>
      </h3>
      <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          <table class="table-container">
                <tr>
                  <td style="border: 0;" align="right">
                 
                  <button type="button" class="btn btn-primary" onclick="fifo_command('display <<');">
                  <i class="bi bi-arrow-bar-left"></i>
                  </button>
                    
                  <button type="button" class="btn btn-primary" onclick="fifo_command('display <');">
                  <i class="bi bi-arrow-left"></i>
                  </button>
                       
                  <button type="button" class="btn btn-primary" onclick="fifo_command('display sel');">
                  SEL
                  </button>                               
                  
                  <button type="button" class="btn btn-primary" onclick="fifo_command('display >');">
                  <i class="bi bi-arrow-right"></i>
                  </button>
                  
                  <button type="button" class="btn btn-primary" onclick="fifo_command('display >>');">
                  <i class="bi bi-arrow-bar-right"></i>
                  </button>
                  
                  <button type="button" class="btn btn-primary" onclick="fifo_command('display back');">
                  <i class="bi bi-backspace-fill"></i>
                  </button>
                  
                  </td>
                </tr>
              </table>
          <table class="table-container">
                <tr>
                  <td>
                    <?php echo "<span style=\"font-weight:600; color: $default_text_color\">Preset</span>"; ?>
                    <div>
                      <input type="button" value="Settings"
                        class="btn-menu"
                        style="margin-left:40px"
                        onclick="fifo_command('display motion_limit');"
                      >

                    <?php
                    if ($servos_enable == "servos_on") {
                        echo "<span style=\"margin-left:20px; margin-right:0px; color: $default_text_color\">Move:";
                        echo "<input type='button' value='One'
                            class='btn-menu'
                            style='margin-left:2px; margin-right:0px;'
                            onclick=\"fifo_command('preset move_one')\">";
                        echo "<input type='button' value='All'
                            class='btn-menu'
                            style='margin-left:4px;'
                            onclick=\"fifo_command('preset move_all')\">";
                        }
                    ?>

                      <input type="button" value="New"
                        class="btn-menu"
                        style="float: right; margin-left:6px"
                        onclick="fifo_command('preset new');"
                      >
                    <?php
                    if ($servos_enable == "servos_on") {
                        echo "<input type='button' value='Copy'
                        class='btn-menu'
                        style='float: right; margin-left:6px'
                        onclick=\"fifo_command('preset copy')\">";
                        }
                    ?>
                      <input type="button" value="Del"
                        class="btn-menu alert-control"
                        style="float: right;margin-left:20px"
                        onclick="fifo_command('preset delete');"
                      >
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo "<span style=\"font-weight:600; color: $default_text_color\"> Time Lapse </span>"; ?>
                    <div>
                    <?php echo "<span style=\"margin-left:40px; font-weight:600; color: $default_text_color\"> Period </span>"; ?>
                      <input type="text" id="tl_period" value="<?php echo time_lapse_period(); ?>" size="3"
                      >
                    <?php echo "<span style=\"margin-left:4px; color: $default_text_color\"> sec </span>"; ?>
                      <input type="button" value="Start"
                        class="btn-menu"
                        onclick="tl_start();"
                        style="float: right; margin-left:10px;"
                      >
                      <input type="button" value="Hold"
                        class="btn-menu"
                        onclick="fifo_command('tl_hold toggle');"
                        style="float: right; margin-left:10px;"
                      >
                      <input type="button" value="End"
                        class="btn-menu alert-control"
                        onclick="fifo_command('tl_end');"
                        style="float: right;"
                      >
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo "<span style=\"font-weight:600; color: $default_text_color\">Config</span>"; ?>
                    <div>
                      <input type="button" value="Motion"
                        class="btn-menu"
                        style="margin-left:40px"
                        onclick="fifo_command('display motion_settings');"
                      >
                      <input type="button" value="Video Res"
                        class="btn-menu"
                        onclick="fifo_command('display video_presets');"
                      >
                      <input type="button" value="Still Res"
                        class="btn-menu"
                        onclick="fifo_command('display still_presets');"
                      >
                      <input type="button" value="Settings"
                        class="btn-menu"
                        onclick="fifo_command('display settings');"
                      >
                      <input type="button" value="Loop"
                        class="btn-menu"
                        onclick="fifo_command('display loop_settings');"
                      >
                      <input type="button" value="Audio"
                        class="btn-menu"
                        onclick="fifo_command('display audio_settings');"
                      >
<?php
if ($servos_enable == "servos_on") {
    echo "<input type='button' value='Servo' class='btn-menu'
            onclick=\"fifo_command('display servo_settings')\">";
    }
?>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo "<span style=\"font-weight:600; color: $default_text_color\"> Camera Params </span>"; ?>
                    <div>
                      <input type="button" value="Picture"
                        class="btn-menu"
                        style= "margin-left:40px"
                        onclick="fifo_command('display picture');"
                      >
                      <input type="button" value="Meter"
                        class="btn-menu"
                        onclick="fifo_command('display metering');"
                      >
                      <input type="button" value="Exposure"
                        class="btn-menu"
                        onclick="fifo_command('display exposure');"
                      >
                      <input type="button" value="White Bal"
                        class="btn-menu"
                        onclick="fifo_command('display white_balance');"
                      >
                      <input type="button" value="Image Effect"
                        class="btn-menu"
                        onclick="fifo_command('display image_effect');"
                      >
                    </div>
                  </td>
                </tr>
              </table>
        </div>
      </div>
    </div>
    <div class="accordion-item">
    <h3 class="accordion-header" id="headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
        Motion Regions
      </button>
    </h3>
    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
      <div class="accordion-body">
              <table class="table-container">
                <tr>
                  <td>
                    <table cellpadding="0" cellspacing="0" border="0" table-layout="fixed">
                    <tr>
                      <td style="border: 0;" >
                         <input type="button" value="List" style="margin-right: 20px;"
                           onclick="list_regions();"
                           class="btn-control"
                         >
                         <input type="text" id="load_regions" size=6>
                         <input type="button" value="Load" style="margin-right: 8px;"
                            onclick="load_regions();"
                            class="btn-menu"
                            >
                      </td>
                      <td style="border: 0;" align="left">
                         <input type="text" id="save_regions" size=6 >
                         <input type="button" value="Save"
                           onclick="save_regions();"
                           class="btn-menu"
                           >
                      </td>
                    </tr>

                    <tr>
                      <td style="border: 0;" align="left">
                      </td>
                       <td style="border: 0;" align="right">
                         <?php echo "<span style=\"color: $default_text_color; margin-left: 12px;\">
                           Coarse Move</span>"; ?>
                         <input type="checkbox" name="move_mode"
                           onclick='move_region_mode(this);' checked>
                       </td>
                    </tr>
 
                   <tr align="right">
                       <td style="border: 0;" align="left">
                         <input type="button" value="New"
                           onclick="new_region();"
                           class="btn-control"
                         >
                         <input type="button" value="Del" style="margin-left: 8px;"
                           onclick="fifo_command('motion delete_regions selected');"
                           class="btn-control alert-control"
                         >
                       </td>
                       <td style="border: 0;" align="right">
                       <?php echo "<span style=\"color: $default_text_color;\">Select</span>"; ?>
                         <input type='image' src='images/arrow0-left.png'
                           style="vertical-align: bottom;"
                           onclick="fifo_command('motion select_region <');"
                         >
                         <input type='image' src='images/arrow0-right.png'
                           style="vertical-align: bottom;"
                           onclick="fifo_command('motion select_region >');"
                         >
                       </td>
                    </tr>
                    </table>
                  </td>

                  <td>
                    <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                       <td style="border: 0;"> </td>
                       <td style="border: 0;" align="center">
                         <input type="image" src="images/arrow-up.png"
                           onclick="move_region(' y m');"
                         >
                       </td>
                       <td style="border: 0;"> </td>

                       <td style="border: 0;"> </td>
                       <td style="border: 0;" align="center">
                         <input type="image" src="images/arrow-up.png"
                           onclick="move_region(' dy p');"
                         >
                       </td>
                       <td style="border: 0;" align="right">
                       </td>
                    </tr>

                    <tr>
                       <td style="border: 0;">
                         <input type="image" src="images/arrow-left.png"
                           onclick="move_region(' x m');"
                         >
                       </td>
                       <td style="border: 0;">
                       <?php echo "<span style=\"color: $default_text_color\">Move</span>"; ?>
                       </td>
                       <td style="border: 0;">
                         <input type="image" src="images/arrow-right.png"
                           onclick="move_region(' x p');"
                         >
                       </td>

                       <td style="border: 0;">
                         <input type="image" src="images/arrow-left.png"
                           onclick="move_region(' dx m');"
                         >
                       </td>
                       <td style="border: 0;" align="center">
                       <?php echo "<span style=\"color: $default_text_color\">Size</span>"; ?>
                       </td>
                       <td style="border: 0;">
                         <input type="image" src="images/arrow-right.png"
                           onclick="move_region(' dx p');"
                         >
                       </td>
                    </tr>

                    <tr>
                       <td style="border: 0;"> </td>
                       <td style="border: 0;" align="center">
                         <input type="image" src="images/arrow-down.png"
                           onclick="move_region(' y p');"
                         >
                       </td>
                       <td style="border: 0;"> </td>

                       <td style="border: 0;"> </td>
                       <td style="border: 0;" align="center">
                         <input type="image" src="images/arrow-down.png"
                           onclick="move_region(' dy m');"
                         >
                       </td>
                       <td style="border: 0;"> </td>

                    </tr>
                    </table>
                  </td>
                </tr>
              </table>
      </div>
    </div>
  </div>
    <div class="accordion-item">
    <h3 class="accordion-header" id="headingThree">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
        System
      </button>
    </h3>
    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
      <div class="accordion-body">
		<?php $version = VERSION;
            echo "<span style=\"font-weight:600; color: $default_text_color\">
             PiKrellCam V${version}: </span>";
          ?>
          <input id="stop_button" type="button" value="Stop"
            onclick="pikrellcam('stop');"
            class="btn-control alert-control"
          >

          <input id="start_button" type="button" value="Start"
            style="margin-left:4px;"
            onclick="pikrellcam('start');"
            class="btn-control"
          >

          <input id="log_button" type="button" value="Log"
            style="margin-left:32px;"
            onclick="window.location='log.php';"
            class="btn-control"
          >
          <a href="help.php"
            class="btn-control" style="margin-left:4px;">Help</a>

          <input id="upgrade_button" type="button" value="Upgrade"
            style="margin-left:48px;"
            onclick="fifo_command('upgrade')"
            class="btn-control"
          >

          <input id="upgrade_button" type="button" value="Reboot"
            style="margin-left:32px;"
            onclick="fifo_command('reboot')"
            class="btn-control alert-control"
          >
          <input id="upgrade_button" type="button" value="Halt"
            style="margin-left:4px;"
            onclick="fifo_command('halt')"
            class="btn-control alert-control"
          >
          <?php
            echo "<span style='float:right;'>";
            if ("$show_audio_controls" == "yes")
                echo "<a href='index.php?hide_audio'>Hide Audio</a>";
            else
                echo "<a href='index.php?show_audio'>Show Audio</a>";
            echo "</span>";
          ?>
      </div>
    </div>
  </div> 
  </div>
</div>
<?php if (file_exists("custom.php")) { include 'custom.php'; } ?>

<script src="js-css/bootstrap.bundle.min.js"></script>
<script>
function toggledarkmode() {
  var element = document.body;
  element.classList.toggle("dark-mode");
}
</script>
</body>
</html>
