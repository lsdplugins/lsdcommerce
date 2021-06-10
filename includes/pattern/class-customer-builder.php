<?php
// TODO :: Builder untuk Customer
class LSDC_DB_Builder {

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
        return new LSDC_DB($this);
    }
}

class LSDC_DB {
    private $sodium;
    private $fat;
    private $carbo;

    static function insert($s) {
        return new LSDC_DB_Builder($s);
    }

    /**
     * It is preferred to call NutritionalFacts::createBuilder
     * to calling this constructor directly.
     */
    // function __construct(LSDC_DB_Builder $b) {
    //     $his->set = $b->set();
    // }
}

echo '<pre>';
var_dump(LSDC_DB::insert('test'));
echo '</pre>';
?>
