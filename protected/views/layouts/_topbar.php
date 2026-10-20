
<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$baseUrl = Url::base();
$identity = Yii::$app->user->identity;

$role = $identity->role;
$username = $identity->username;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-black shadow-sm">
    <div class="container-fluid">

        <!-- Logo -->
        <div class="col-lg-2 col-md-2 col-sm-12 p-0">
            <a class="navbar-brand" href="<?= $baseUrl ?>/">
                <img
                    src="<?= $baseUrl ?>/theme/assets/images/logo.png"
                    alt="Logo"
                    height="35"
                >
            </a>
        </div>

        <!-- Menu -->
        <div class="col-lg-8 col-md-8 col-sm-12 p-0">

            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarMenu"
                aria-controls="navbarMenu"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div
                class="collapse navbar-collapse justify-content-center"
                id="navbarMenu"
            >
                <ul class="navbar-nav">

                    <?php if ($role == User::ROLE_SUPERADMIN): ?>

                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseUrl ?>/">
                                <i class="fa fa-home"></i> Dashboard
                            </a>
                        </li>

                        <!-- Master -->
                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-toggle="dropdown"
                            >
                                <i class="fa fa-cogs"></i> Master
                            </a>

                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= $baseUrl ?>/agent">Agent</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/bank">Bank</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/employee-class">Employee Class</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/product">Product</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/medical">Medical</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/claim-reason">Claim Reason</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/disease">Disease</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/place-of-death">Place of Death</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/reassuradur">Reassuradur</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/global-reas">Global Reas</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/signature">Signature</a>
                                <a class="dropdown-item" href="<?= $baseUrl ?>/user">User</a>
                            </div>
                        </li>

                        <!-- Quotation -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseUrl ?>/quotation">
                                <i class="fa fa-quote-right"></i> Quotation
                            </a>
                        </li>

                    <?php endif; ?>


                    <!-- New Business: SUPERADMIN, UW, PUSAT -->
                    <?php if (in_array($role, [
                        User::ROLE_SUPERADMIN,
                        User::ROLE_UW,
                        User::ROLE_PUSAT
                    ])): ?>

                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-toggle="dropdown"
                            >
                                <i class="fa fa-files-o"></i> New Business
                            </a>

                            <div class="dropdown-menu">

                                <?php if ($role == User::ROLE_SUPERADMIN): ?>
                                    <a class="dropdown-item" href="<?= $baseUrl ?>/policy">
                                        Policy
                                    </a>
                                <?php endif; ?>

                                <a class="dropdown-item" href="<?= $baseUrl ?>/member">
                                    Member
                                </a>

                                <a class="dropdown-item" href="<?= $baseUrl ?>/billing">
                                    Billing
                                </a>

                                <?php if ($role == User::ROLE_SUPERADMIN): ?>
                                    <a class="dropdown-item" href="<?= $baseUrl ?>/create-member">
                                        Add Member
                                    </a>
                                <?php endif; ?>

                            </div>
                        </li>

                    <?php endif; ?>


                    <!-- Alteration: SUPERADMIN -->
                    <?php if ($role == User::ROLE_SUPERADMIN): ?>

                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-toggle="dropdown"
                            >
                                <i class="fa fa-pencil"></i> Alteration
                            </a>

                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= $baseUrl ?>/alteration-refund">
                                    Refund
                                </a>

                                <a class="dropdown-item" href="<?= $baseUrl ?>/alteration-endorsement">
                                    Endorsement
                                </a>

                                <a class="dropdown-item" href="<?= $baseUrl ?>/alteration-cancel">
                                    Cancel
                                </a>
                            </div>
                        </li>

                    <?php endif; ?>


                    <!-- Claim: SUPERADMIN, CLAIM -->
                    <?php if (in_array($role, [
                        User::ROLE_SUPERADMIN,
                        User::ROLE_CLAIM
                    ])): ?>

                        <li class="nav-item">
                            <a class="nav-link" href="<?= $baseUrl ?>/claim">
                                <i class="fa fa-dropbox"></i> Claim
                            </a>
                        </li>

                    <?php endif; ?>


                    <!-- Report: SUPERADMIN, UW, PUSAT -->
                    <?php if (in_array($role, [
                        User::ROLE_SUPERADMIN,
                        User::ROLE_UW,
                        User::ROLE_PUSAT
                    ])): ?>

                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-toggle="dropdown"
                            >
                                <i class="fa fa-book"></i> Report
                            </a>

                            <div class="dropdown-menu">

                                <?php if ($role == User::ROLE_SUPERADMIN): ?>

                                  

                                    <a class="dropdown-item" href="<?= $baseUrl ?>/data-produksi">
                                        Data Produksi
                                    </a>
									
									  <a class="dropdown-item" href="<?= $baseUrl ?>/report-billing">
                                        Summary Produksi
                                    </a>

                                    <a class="dropdown-item" href="<?= $baseUrl ?>/restitusi">
                                        Restitusi
                                    </a>

                                    <a class="dropdown-item" href="<?= $baseUrl ?>/member-claim">
                                        Member Claim
                                    </a>
									

                                <?php else: ?>

                                    <a class="dropdown-item" href="<?= $baseUrl ?>/data-produksi">
                                        Data Produksi
                                    </a>

                                <?php endif; ?>

                            </div>
                        </li>

                    <?php endif; ?>


                    <!-- Master: REAS -->
                    <?php if ($role == User::ROLE_REAS): ?>

                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-toggle="dropdown"
                            >
                                <i class="fa fa-cogs"></i> Master
                            </a>

                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= $baseUrl ?>/reassuradur">
                                    Reassuradur
                                </a>

                                <a class="dropdown-item" href="<?= $baseUrl ?>/global-reas">
                                    Global Reas
                                </a>
                            </div>
                        </li>

                    <?php endif; ?>


                    <!-- Master: AKTUARI -->
                    <?php if ($role == User::ROLE_AKTUARI): ?>

                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-toggle="dropdown"
                            >
                                <i class="fa fa-cogs"></i> Master
                            </a>

                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= $baseUrl ?>/product">
                                    Product
                                </a>
                            </div>
                        </li>

                    <?php endif; ?>


                    <!-- Master: CLAIM -->
                    <?php if ($role == User::ROLE_CLAIM): ?>

                        <li class="nav-item dropdown">
                            <a
                                class="nav-link dropdown-toggle"
                                href="#"
                                data-toggle="dropdown"
                            >
                                <i class="fa fa-cogs"></i> Master
                            </a>

                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="<?= $baseUrl ?>/claim-reason">
                                    Claim Reason
                                </a>

                                <a class="dropdown-item" href="<?= $baseUrl ?>/disease">
                                    Disease
                                </a>

                                <a class="dropdown-item" href="<?= $baseUrl ?>/place-of-death">
                                    Place of Death
                                </a>
                            </div>
                        </li>

                    <?php endif; ?>

                </ul>
            </div>
        </div>

        <!-- User -->
        <div class="col-lg-2 col-md-2 text-right p-0">
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item dropdown">

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        data-toggle="dropdown"
                    >
                        <i class="fa fa-user-circle"></i>
                        <?= Html::encode($username) ?>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right">
                        <?= Html::a(
                            '<i class="fa fa-sign-out"></i> Logout',
                            ['site/logout'],
                            [
                                'class' => 'dropdown-item',
                                'data-method' => 'post'
                            ]
                        ) ?>
                    </div>

                </li>
            </ul>
        </div>

    </div>
</nav>
