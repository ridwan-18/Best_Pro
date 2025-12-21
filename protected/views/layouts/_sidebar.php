<?php

use yii\helpers\Url;
use app\models\User;
?>

<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="slimscroll-menu" id="remove-scroll">
        <!-- LOGO -->
        <div class="topbar-left text-center pl-0">
            <a href="#" class="logo">
                <span>
                    <img src="<?= Url::base() . '/theme/assets/images/logo.png'; ?>" alt="" height="28">
                </span>
                <i>
                    <img src="<?= Url::base() . '/theme/assets/images/logo_sm.png'; ?>" alt="" height="24">
                </i>
            </a>
        </div>
        <!-- User box -->
        <div class="user-box text-center pb-0">
            <div class="user-img mx-auto">
                <img src="<?= Url::base() . '/theme/assets/images/users/avatar-1.png'; ?>" alt="user-img" class="rounded-circle img-fluid">
            </div>
            <h5><a href="#"><?= Yii::$app->user->identity->name; ?></a> </h5>
           
        </div>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu" id="side-menu">
                <li class="menu-title">Menu</li>
                <?php
                if (Yii::$app->user->identity->role == User::ROLE_SUPERADMIN) :
                ?>
                    <li>
                        <a href="<?= Url::base() . '/'; ?>">
                            <i class="fa fa-home"></i> <span> Dashboard </span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-cogs"></i> <span> Master </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/agent'; ?>">Agent</a></li>
                            <li><a href="<?= Url::base() . '/bank'; ?>">Bank</a></li>
                            <li><a href="<?= Url::base() . '/employee-class'; ?>">Employee Class</a></li>
                            <li><a href="<?= Url::base() . '/product'; ?>">Product</a></li>
                            <li><a href="<?= Url::base() . '/medical'; ?>">Medical</a></li>
                            <li><a href="<?= Url::base() . '/claim-reason'; ?>">Claim Reason</a></li>
                            <li><a href="<?= Url::base() . '/disease'; ?>">Disease</a></li>
                            <li><a href="<?= Url::base() . '/place-of-death'; ?>">Place of Death</a></li>
                            <li><a href="<?= Url::base() . '/reassuradur'; ?>">Reassuradur</a></li>
                            <li><a href="<?= Url::base() . '/global-reas'; ?>">Global Reas</a></li>
                            <li><a href="<?= Url::base() . '/signature'; ?>">Signature</a></li>
                            <li><a href="<?= Url::base() . '/user'; ?>">User</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= Url::base() . '/quotation'; ?>">
                            <i class="fa fa-quote-right"></i> <span> Quotation </span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-files-o"></i> <span> New Business </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/policy'; ?>">Policy</a></li>
                            <li><a href="<?= Url::base() . '/member'; ?>">Member</a></li>
                            <li><a href="<?= Url::base() . '/billing'; ?>">Blling</a></li>
							<!-- update edo 14-08-2023  -->
							<!-- batas update edo 14-08-2023  -->
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-pencil"></i> <span> Alteration </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/alteration-refund'; ?>">Refund</a></li>
                            <li><a href="<?= Url::base() . '/alteration-endorsement'; ?>">Endorsement</a></li>
                            <li><a href="<?= Url::base() . '/alteration-cancel'; ?>">Cancel</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= Url::base() . '/claim'; ?>">
                            <i class="fa fa-dropbox"></i> <span> Claim </span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-book"></i> <span> Report </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/report-billing'; ?>">Billing</a></li>
                            <li><a href="<?= Url::base() . '/data-produksi'; ?>">Data produksi</a></li>
							<li><a href="<?= Url::base() . '/member-claim'; ?>">Member claim</a></li>
                        </ul>
                    </li>
                <?php
                endif;
				
				
                if (Yii::$app->user->identity->role == User::ROLE_UW) :
                ?>
				<!-- 
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-cogs"></i> <span> Master </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/agent'; ?>">Agent</a></li>
                            <li><a href="<?= Url::base() . '/employee-class'; ?>">Employee Class</a></li>
                            <li><a href="<?= Url::base() . '/medical'; ?>">Medical</a></li>
                        </ul>
                    </li>
					-->
					
					
					<!-- 
                    <li>
                        <a href="<?= Url::base() . '/quotation'; ?>">
                            <i class="fa fa-quote-right"></i> <span> Quotation </span>
                        </a>
                    </li>
					-->
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-files-o"></i> <span> New Business </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
							<!--   -->
							<!-- <li><a href="<?= Url::base() . '/view-member'; ?>">View Member</a></li>  -->
							<!-- batas update edo 14-08-2023  -->
                            <!--  <li><a href="<?= Url::base() . '/policy'; ?>">Policy</a></li>   -->
                            <li><a href="<?= Url::base() . '/member'; ?>">Member</a></li>
                            <li><a href="<?= Url::base() . '/billing'; ?>">Blling</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-pencil"></i> <span> Alteration </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/alteration-refund'; ?>">Refund</a></li>
                            <li><a href="<?= Url::base() . '/alteration-endorsement'; ?>">Endorsement</a></li>
                            <li><a href="<?= Url::base() . '/alteration-cancel'; ?>">Cancel</a></li>
                        </ul>
                    </li>
					
					<li>
                        <a href="<?= Url::base() . '/claim'; ?>">
                            <i class="fa fa-dropbox"></i> <span> Claim </span>
                        </a>
                    </li>
					
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-book"></i> <span> Report </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                              <!--  <li><a href="<?= Url::base() . '/report-billing'; ?>">Billing</a></li>  -->
							<li><a href="<?= Url::base() . '/data-produksi'; ?>">Data produksi</a></li>
							<!-- <li><a href="<?= Url::base() . '/member-claim'; ?>">Member claim</a></li> -->
                        </ul>
                    </li>					
                <?php
                endif;
				
				if (Yii::$app->user->identity->role == User::ROLE_PUSAT) :
                ?>
				<!-- 
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-cogs"></i> <span> Master </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/agent'; ?>">Agent</a></li>
                            <li><a href="<?= Url::base() . '/employee-class'; ?>">Employee Class</a></li>
                            <li><a href="<?= Url::base() . '/medical'; ?>">Medical</a></li>
                        </ul>
                    </li>
					-->
					
					
					<!-- 
                    <li>
                        <a href="<?= Url::base() . '/quotation'; ?>">
                            <i class="fa fa-quote-right"></i> <span> Quotation </span>
                        </a>
                    </li>
					-->
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-files-o"></i> <span> New Business </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
							<!--   -->
							<!-- <li><a href="<?= Url::base() . '/view-member'; ?>">View Member</a></li>  -->
							<!-- batas update edo 14-08-2023  -->
                            <!--  <li><a href="<?= Url::base() . '/policy'; ?>">Policy</a></li>   -->
                            <li><a href="<?= Url::base() . '/member'; ?>">Member</a></li>
                            <li><a href="<?= Url::base() . '/billing'; ?>">Blling</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-pencil"></i> <span> Alteration </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/alteration-refund'; ?>">Refund</a></li>
                            <li><a href="<?= Url::base() . '/alteration-endorsement'; ?>">Endorsement</a></li>
                            <li><a href="<?= Url::base() . '/alteration-cancel'; ?>">Cancel</a></li>
                        </ul>
                    </li>
					
					<li>
                        <a href="<?= Url::base() . '/claim'; ?>">
                            <i class="fa fa-dropbox"></i> <span> Claim </span>
                        </a>
                    </li>
					
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-book"></i> <span> Report </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                              <!--  <li><a href="<?= Url::base() . '/report-billing'; ?>">Billing</a></li>  -->
							<li><a href="<?= Url::base() . '/data-produksi'; ?>">Data produksi</a></li>
							<!-- <li><a href="<?= Url::base() . '/member-claim'; ?>">Member claim</a></li> -->
                        </ul>
                    </li>					
                <?php
                endif;
				
				
				
				
				
                if (Yii::$app->user->identity->role == User::ROLE_REAS) :
                ?>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-cogs"></i> <span> Master </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/reassuradur'; ?>">Reassuradur</a></li>
                            <li><a href="<?= Url::base() . '/global-reas'; ?>">Global Reas</a></li>
                        </ul>
                    </li>
                <?php
                endif;
                if (Yii::$app->user->identity->role == User::ROLE_AKTUARI) :
                ?>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-cogs"></i> <span> Master </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/product'; ?>">Product</a></li>
                        </ul>
                    </li>
                <?php
                endif;
                if (Yii::$app->user->identity->role == User::ROLE_CLAIM) :
                ?>
                    <li>
                        <a href="javascript: void(0);"><i class="fa fa-cogs"></i> <span> Master </span> <span class="menu-arrow"></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="<?= Url::base() . '/claim-reason'; ?>">Claim Reason</a></li>
                            <li><a href="<?= Url::base() . '/disease'; ?>">Disease</a></li>
                            <li><a href="<?= Url::base() . '/place-of-death'; ?>">Place of Death</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?= Url::base() . '/claim'; ?>">
                            <i class="fa fa-dropbox"></i> <span> Claim </span>
                        </a>
                    </li>
                <?php
                endif;
                ?>
            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>
<!-- Left Sidebar End -->