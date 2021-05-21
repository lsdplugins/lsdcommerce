<?php
// TODO :: Builder untuk Customer
class LSDD_DB_Builder {

    private $name;

    private $phone;

    private $email;



    // function fat($f) {
    //     $this->fat = $f;
    //     return $this;
    // }

    function insert($c) {
        $this->set = $c;
        return $this;
    }

    // function getSodium() {
    //     return $this->sodium;
    // }

    // function getFat() {
    //     return $this->fat;
    // }

    // function getCarbo() {
    //     return $this->carbo;
    // }

    function build() {
        return new LSDD_DB($this);
    }
}

class LSDD_DB {
    private $sodium;
    private $fat;
    private $carbo;

    static function insert($s) {
        return new LSDD_DB_Builder($s);
    }

    /**
     * It is preferred to call NutritionalFacts::createBuilder
     * to calling this constructor directly.
     */
    // function __construct(LSDD_DB_Builder $b) {
    //     $his->set = $b->set();
    // }
}

echo '<pre>';
var_dump(LSDD_DB::insert('test'));
echo '</pre>';
?>
