<?php echo '<?php' . "\n"; ?>
class Mpp_Infusionsoft_Generated_<?php echo $this->table; ?> extends Mpp_Infusionsoft_Generated_Base{
    protected static $tableFields = <?php echo $this->getFieldsAsArraySource(); ?>;


    public function __construct($id = null, $app = null){
    	parent::__construct('<?php echo $this->table; ?>', $id, $app);
    }

    public function getFields(){
		return self::$tableFields;
	}

	public function addCustomField($name){
		self::$tableFields[] = $name;
	}
}
