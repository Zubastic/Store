<?php
// Это не захотелось впихиваться в массив, поэтому решил оставить массив в массиве.
// TODO: Впихнуть, где не впихивалось.
    class Category {
        public function __construct($name, $ref) {
            $this->Name = $name;
            $this->Reference = $ref;
        }

        public $Name = "";
        public $Reference = "";
    }
?>
