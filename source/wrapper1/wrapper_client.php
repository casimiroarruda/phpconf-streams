<?php
require 'InhameWrapper.php';
stream_wrapper_register('inhame', 'InhameWrapper');
echo file_get_contents('inhame://oi_eu_sou_o.Goku');