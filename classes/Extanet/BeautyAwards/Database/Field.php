<?phpnamespace Extanet\BeautyAwards\Database;class Field {    var $name;    var $type;    function __construct($name, $type) {        $this->name = $name;        $this->type = $type;    }}