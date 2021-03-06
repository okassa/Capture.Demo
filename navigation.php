<?php
// ------------------------------------------------------------
// navigation.php.
// Display the content of the navigation bar.
// Not meant to be a top-level file.
// Included by common.php.
// ------------------------------------------------------------
?>

<script type="text/javascript">
   jQuery(document).ready(function($) {

        $('#signin_link').fancybox({
            padding: 0,
            scrolling: 'no',
            autoScale: true,
            autoDimensions: false
        });

        $('#profile_link').fancybox({
            padding: 0,
            scrolling: 'no',
            autoScale: true,
            autoDimensions: false,
            width:675
        });

   })
</script>

<script type="text/javascript">
      Backplane.init({
        "serverBaseURL": "http://api.js-kit.com/v1",
        "busName": "rpxstaging"
      });
</script>

<?php
// Display navigation bar based on $user_entity.
// If $user_entity is NULL, then user is not logged in, so just display the signin link.
// Otherwise, display welcome message and links such as home, editprofile, and logout.
function make_navigation_bar($user_entity, $page_name = NULL)
{
  global $options;
  echo "<div id='navigation'>\n";

  if (isset($options['captureui_addrs'])) {
    make_app_addrs_list();
  }

  // User is already logged in, so
  //   - access user information and display welcome message
  //   - display 'home', 'editprofile', and 'logout' links.

  if (isset($user_entity)) {
    if (isset($user_entity['stat']) && $user_entity['stat'] == 'ok') {
      if (isset($_COOKIE['app'])) {
        echo "<span id='app_name'>" . $_COOKIE['app'] . "</span>&nbsp;";
      }
      $user_entity = $user_entity['result'];

      if ($page_name != "home")
        echo "<a href='.' id='home'>Home</a>";

      echo "Hello, " . $user_entity['displayName'] . "";

      print '&nbsp;|&nbsp;';

      if ($options['edit_profile_with_fancybox']) {
          echo '<a href="profile_with_token_refresh.php" id="profile_link" class="iframe">Edit Profile</a>';
      }
      else {
          if ($page_name != "editprofile") {
              echo "<a href='editprofile.php' id='edit_profile'>Edit Your Profile</a>";
          } else {
              echo "Edit Your Profile";
          }
      }

      // if ($page_name != "rawprofile") {
      //   echo "&nbsp;|&nbsp; <a href='rawprofile.php' id='raw_profile'>View Capture Profile</a>";
      // } else {
      //   echo "&nbsp;|&nbsp; View Capture Profile";
      // }

      if ($page_name != "public_profile") {
        echo "&nbsp;|&nbsp; <a href='public_profile.php' id='public_profile'>View Public Profile</a>";
      } else {
        echo "&nbsp;|&nbsp; View Public Profile";
      }
    } else {
      debug_out("*** Bad entity!<br>\n");
      debug_raw_data($user_entity);
    }

    if (isset($options['sso_server'])) {
      echo "&nbsp;|&nbsp; <a href='#' id='logout' onClick='sso_logout()'>Sign Out</a>";
    } else {
      echo "&nbsp;|&nbsp; <a href='logout.php' id='logout'>Sign Out</a>";
    }
  }

  // User is not logged in, so display signin link
  else {
    make_signin_link();
  }
  echo "</div>\n";
}

function make_signin_link()
{
  global $options;
  $app_addr = $options['captureui_addr'];

  $args = array ( 'response_type'   => 'code',
                  'redirect_uri'    => $options['my_addr'] . "/oauth_redirect.php",
                  'client_id'       => $options['client_id'],
                  'xd_receiver'     => $options['my_addr'] . "/xdcomm.html",
                  'recover_password_callback' => 'CAPTURE.recoverPasswordCallback'); //optionally pass in callback

  //echo "<iframe id='signin' style='display:none;' src='http://$app_addr/oauth/signin?" . http_build_query($args) . "' scrolling='no' width='600' height='450' frameborder='0'></iframe>\n";

  echo "<a id='signin_link' class='iframe' href='$app_addr/oauth/signin?" . http_build_query($args) . "'>Register / Sign In</a><br>\n\n";
  echo '
  <script type="text/javascript">
     document.getElementById("signin_link").href+="&bp_channel="+encodeURIComponent(Backplane.getChannelID());
  </script>
  ';
}

function make_app_addrs_list()
{
  global $options;
  $app_addrs = $options['captureui_addrs'];
  $captureui_name = $_SERVER['QUERY_STRING'];
  // parse_str($captureui_name);
  if (isset($_COOKIE['app'])) {
      $app = $_COOKIE['app'];
  }
  else{
      $app = "demo";
  }

  if (sizeof($app_addrs) > 1) {
    echo "<select id='app_addrs'>";
    foreach ($app_addrs as $key => $value) {
      if($key == $app) {
        $selected = "selected='yes' ";
      } else {
        $selected = "";
      }
      echo  "<option " . $selected . "value=" . $key . ">" . $key . " ==> " . $value . "</option>";
    }
    echo "</select>&nbsp;&nbsp;";
  }
}




?>
