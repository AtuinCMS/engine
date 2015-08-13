    <div class="main-sidebar sidebar-light">
        <!-- Inner sidebar -->
        <div class="sidebar">
            <!-- user panel (Optional) -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
                </div>
                <div class="pull-left info">
                    <p>User Name</p>

                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
        </div>
        <!-- /.sidebar -->
        <!--        <div class="sidebar">-->
        <?php

        echo atuin\menus\Widget::widget([
            'header' => 'HEADER',
            'options' => ['class' => 'sidebar-menu'],
            'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span></a>',
            'submenuTemplate' => '<ul class="treeview-menu">{items}</ul>',
            'labelTemplate' => '<a href="#">{icon}<span>{label}</span></a>',
            'iconPrefix' => 'fa fa-',
            'parentTemplate' => '<a href="#">{icon}<span>{label}</span><i class="fa fa-angle-left pull-right"></i> </a>'
        ]);

        ?>
        <!--        </div>-->
    </div>