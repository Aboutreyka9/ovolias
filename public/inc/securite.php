<?php

if (!(isset($_SESSION['client']))) {
    header('location: '.RACINE);
}
