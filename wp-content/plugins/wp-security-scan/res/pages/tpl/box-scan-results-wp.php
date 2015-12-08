<?php if(! WsdUtil::canLoad()) { return; } ?>
<ul class="acx-common-list">
    <?php
        // display wp info
        $class = new ReflectionClass('WsdInfo');
        $methods = $class->getMethods();
        if(! empty($methods)){
            foreach($methods as $method){
                echo '<li><p>'.call_user_func(array($method->class, $method->name)).'</p></li>';
            }
        }
    ?>
</ul>
