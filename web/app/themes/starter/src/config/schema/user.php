<?php

use Coretik\Core\Builders\UserType;

// Register administrator user type
app()->schema()->register(
    new UserType('administrator', 'Administrateur', ['allow_all'])
);

