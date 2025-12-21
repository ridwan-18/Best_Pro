<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>

<!-- Top Bar Start -->
<div class="topbar">
    <nav class="navbar-custom">
        <ul class="list-unstyled topbar-right-menu float-right mb-0">
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                   <img src="<?= Url::base() . '/theme/assets/images/users/avatar-1.png'; ?>" alt="user" class="rounded-circle"> <span class="ml-1"><?= Yii::$app->user->identity->username; ?> <i class="mdi mdi-chevron-down"></i> </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <div class="dropdown-item noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>
                    <!-- item-->
                    <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fi-cog"></i> <span>Settings</span>
                    </a> -->
                    <!-- item-->
                    <?= Html::a('<i class="fi-power"></i> <span>Logout</span>', ['site/logout'], [
                        'class' => 'dropdown-item notify-item',
                        'data-method' => 'post',
                    ]) ?>
                </div>
            </li>
        </ul>
        <ul class="list-inline menu-left mb-0">
            <li class="float-left">
                <button class="button-menu-mobile open-left disable-btn">
                    <i class="dripicons-menu"></i>
                </button>
            </li>
        </ul>
    </nav>
</div>
<!-- Top Bar End -->