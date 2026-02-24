<?php
// Data: $profile, $themeData provided by controller
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/Version.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/iconpicker.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/start.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/App.php';

use App\DB;
use App\Themes\UserTheme;
use App\App;
// App::dump($profile, 'Profile data for theme tester§');
if (!isset($profile) || !is_array($profile)) {
    $profile = [
        'profile_name' => 'Unknown',
        'profile_image' => '',
        'bio' => '',
        'links' => [],
        'verified' => 0,
        'locked' => 0,
    ];
}
// Always get the user's chosen theme if possible
if (!isset($themeData) && isset($profile['user_id'])) {
    if (isset($config['db'])) {
        // error_log('DEBUG: public_profile.php DB config: ' . print_r($config['db'], true));
        $db = $db ?? new DB($config['db']);
    } else {
        $db = null;
    }
    if ($db && class_exists('App\\Themes\\UserTheme')) {
        $themeService = new UserTheme($db, $config);
        $themeData = $themeService->get_profile_theme($profile['user_id']);
    }
}
// App::dump($theme_list);
require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/profile_preview_header.php';
// App::dump($themeData, 'Theme data for theme tester');
// die();
?>
<div class="theme-tester-bar w-100 container-fluid d-flex justify-content-center align-items-center py-2" style=" border: none;">
    <div class="d-flex align-items-center gap-2">
        <label for="themeSelect" class="me-2 mb-0">Select Theme:</label>
        <select id="themeSelect" class="form-select d-inline-block w-auto">
            <?php foreach ($theme_list as $theme): ?>
                <?php
                $selected = '';
                if ($themeData['theme_id'] == $theme['theme_id']) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                echo '<option data-path="' . htmlspecialchars($theme['theme_path']) . '" value="' . htmlspecialchars($theme['theme_id']) . '" ' . $selected . '>' . htmlspecialchars(ucfirst($theme['theme_title'])) . '</option>';
                ?>
            <?php endforeach; ?>
        </select>
        <button id="applyThemeBtn" class="btn btn-primary ms-2">Apply</button>
        <span id="themeApplyMsg" class="ms-2"></span>
        <a href="/user/dashboard" class="btn btn-secondary ms-3">Back to Dashboard</a>
    </div>
</div>
<!-- Page Content -->

<?php
if ($profile['locked'] > 0) {
?>
    <div class="container"></div>
    <div class="row">
        <div class="col-lg-6 mx-auto text-center" id="bio">
            <?php
            echo '<i class="fa fa-solid fa-lock"></i> This profile is locked and not available at this time'
            ?>
        </div>
    </div>
<?php

} else {
?>
    <nav class="navbar profileNav navbar-expand-lg static-top">
        <div class="container text-center">
            <a class="navbar-brand mx-auto" href="https://lazylinks.co.uk?utm_source=linktree&amp;utm_medium=profile&amp;utm_content=Lazysod">
                <?php
                if ($profile['pride_logo'] > 0) {
                    $logo = 'lazylink-pride.png';
                } else {
                    $logo = 'lazylink.png';
                }
                echo '<img src=" ' . $config['base_url'] . '/images/' .  $logo . ' " class="mx-auto img-fluid">';
                ?>
            </a>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto text-center" id="error"></div>
        </div>
        <div class="row">
            <div class="mx-auto col-md-6 col-sm-12 text-center">
                <img src="<?php echo $config['base_url'] . '/app/uploads/img/profile/' .  $profile['profile_image']; ?>" class="mx-auto img-fluid avatar">
                <h1 class="tprofile_name">@<?php echo $profile['profile_name'];
                                            if ($profile['verified'] > 0) {
                                                echo '<img src="' . $config['base_url'] . '/app/uploads/img/verified.png" class="verified" data-toggle="tooltip" data-placement="top" title="Verified!">';
                                            } ?>
            </div>
        </div>
        <?php if (isset($event['ev_status']) && $event['ev_status'] > 0) { ?>
            <div class="row mb-3">
                <div class="mx-auto col-md-6 dark-box">
                    <h3 class="text-center text-white"><a href="#" class="text-white" data-toggle="modal" data-target="#eventModal"><img src="<?php echo $config['base_url'] . '/images/event.png'; ?>" class="img-fluid" style="width: 100px;"> Check out my event!</h3></a>
                </div>
            </div>
        <?php } ?>
        <?php if ($profile['bio'] != '') { ?>
            <div class="row">
                <div class="col-lg-6 mx-auto text-center" id="bio">
                    <?php
                    echo nl2br($profile['bio']);
                    ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <div class="row">


            <?php
            if ($profile['locked'] < 1) {

                $iconpicker = new \App\iconpicker;
                if (isset($LinkGroups) && is_array($LinkGroups) && count($LinkGroups) > 0) {
                    // Grouped mode: grid layout
                    echo '<div class="row">';
                    foreach ($LinkGroups as $lg) {
                        echo '<div class="col-lg-6 col-md-6 mb-4">';
                        // echo '<div class="">';
                        // echo '<div class="">';
                        echo '<h4 class="card-title">' . htmlspecialchars($lg['group_name']) . '</h4>';
                        if (isset($lg['links']) && is_array($lg['links']) && count($lg['links']) > 0) {
                            echo '<div class="list-group">';
                            foreach ($lg['links'] as $link) {
                                if ($link['is_email']) {
                                    echo '<a href="mailto:' . e($link['link_url']) . '" title="' . e($link['link_title']) . '" class="link-item list-group-item list-group-item-action"><span class="iconSpan"><i class="fa-solid fa-at fa-lg"></i></span>' . e($link['link_title']) . '</a>';
                                } else if ($link['adult'] > 0) {
                                    echo '<a href="' . $link['link_url'] . '" data-url="' . $link['link_url'] . '" data-target="#nsfwModal" data-toggle="modal" title="' . $link['link_title'] . '" class="adultLink link-item list-group-item list-group-item-action"><span class="iconSpan">' . $iconpicker->get($link['link_url'], $link['link_title']) . '</span><i class="fa-solid fa-lock"></i>  ' . e($link['link_title']) . '!!</a>';
                                } else {
                                    echo '<a href="' . $link['link_url'] . '" title="' . $link['link_title'] . '" class="link-item list-group-item list-group-item-action"><span class="iconSpan">' . $iconpicker->get($link['link_url'], $link['link_title']) . '</span>' . e($link['link_title']) . '</a>';
                                }
                            }
                            echo '</div>';
                        } else {
                            echo '<p>No links in this group.</p>';
                        }
                        // echo '</div></div></div>';
                        echo '</div>';
                    }
                    echo '</div>';
                } elseif (isset($LinkList) && is_array($LinkList) && count($LinkList) > 0) {
                    // Non-grouped mode: single centered column
                    echo '<div class="row justify-content-center">';
                    echo '<div class="col-lg-6 col-md-8">';
                    echo '<div class="list-group" id="linkList">';
                    foreach ($LinkList as $link) {
                        if ($link['is_email']) {
                            echo '<a href="mailto:' . e($link['link_url']) . '" title="' . e($link['link_title']) . '" class="link-item list-group-item list-group-item-action"><span class="iconSpan"><i class="fa-solid fa-at fa-lg"></i></span>' . e($link['link_title']) . '</a>';
                        } else if ($link['adult'] > 0) {
                            echo '<a href="' . $link['link_url'] . '" data-url="' . $link['link_url'] . '" data-target="#nsfwModal" data-toggle="modal" title="' . $link['link_title'] . '" class="adultLink link-item list-group-item list-group-item-action"><span class="iconSpan">' . $iconpicker->get($link['link_url'], $link['link_title']) . '</span><i class="fa-solid fa-lock"></i>  ' . e($link['link_title']) . '!!</a>';
                        } else {
                            echo '<a href="' . $link['link_url'] . '" title="' . $link['link_title'] . '" class="link-item list-group-item list-group-item-action"><span class="iconSpan">' . $iconpicker->get($link['link_url'], $link['link_title']) . '</span>' . e($link['link_title']) . '</a>';
                        }
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    if(isset($profile['message'])){
                        echo $profile['message'];
                    }else{
                        echo '<div class="alert alert-info">This profile has no links yet...Perhaps try again later?</div>';
                    }
                }
            }
            ?>


    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6 mx-auto text-center">

        </div>
    </div>
    </div>
    <!-- Move JS to end of body to ensure DOM is ready -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var applyBtn = document.getElementById('applyThemeBtn');
        var themeSelect = document.getElementById('themeSelect');
        var msg = document.getElementById('themeApplyMsg');
        if (applyBtn && themeSelect) {
            applyBtn.addEventListener('click', function() {
                var themeId = themeSelect.value;
                applyBtn.disabled = true;
                msg.textContent = 'Saving...';
                fetch('/ajax/applyTheme.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ theme_id: themeId })
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        msg.textContent = 'Theme applied!';
                        msg.className = 'text-success ms-2';
                    } else {
                        msg.textContent = result.error || 'Error applying theme.';
                        msg.className = 'text-danger ms-2';
                    }
                })
                .catch(() => {
                    msg.textContent = 'Error applying theme.';
                    msg.className = 'text-danger ms-2';
                })
                .finally(() => {
                    setTimeout(() => { msg.textContent = ''; applyBtn.disabled = false; }, 2000);
                });
            });
        }
    });
    </script>
    <script src="/js/public_profile.js"></script>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/views/partials/footer.php'; ?>