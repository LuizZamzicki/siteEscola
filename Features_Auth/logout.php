<?php
session_start();
session_destroy();
header('Location: ?param=login');
exit();
