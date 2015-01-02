<?php

class KT_CRUD_Admin_Column {
    
    const TEXT_TYPE = "text"; // Běžný sloupec s textem
    const EDIT_LINK_TYPE = "edit-link"; // Z hodnoty se vykreslí odkaz na url adresu detailu včetně odkazu pro delete
    const SWITCH_BUTTON_TYPE = "switch-button"; // Vykreslí switch input s názvem sloupce a hodnout 1 / 2
    const IMAGE_TYPE = "image"; // na základě ID attachmentu v rámci WP vykrlesí img tag s obrázkem
    const CUSTOM_TYPE = "custom"; // bude se volat custom callback function (respektive filter)
    
    private $name = null;
    private $label = null;
    private $position = 0;
    private $type = self::TEXT_TYPE;
    private $prefix = null;
    private $suffix = null;
    private $customCallbackFunction = null;
    private $deletable = false;
    
    /**
     * @param string $name
     * @return \KT_CRUD_Admin_Column
     */
    public function __construct( $className ) {
        $this->setName($className);
        
        return $this;
    }
    
    // --- gettery a settery ------------------
    
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Nastaví název sloupce z databáze.
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $name
     * @return \KT_CRUD_Admin_Column
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }
    
    /**
     * Nastaví popisek sloupce, který se zobrazí v hlavičce tabulky
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $label
     * @return \KT_CRUD_Admin_Column
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Nastaví pořadí sloupce v tabulce zařeno 1,2,3...n
     *  
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param int $position
     * @return \KT_CRUD_Admin_Column
     */
    public function setPosition($position) {
        if( ! kt_try_get_int($position)){
            throw new InvalidArgumentException("position have to by an int type");
        }
        $this->position = kt_try_get_int($position);
        return $this;
    }
    
    /**
     * @return string
     */
    private function getType() {
        return $this->type;
    }

    /**
     * Nastaví, o jaký typ sloupce se jedná - viz constanty třídy
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param type $type
     * @return \KT_CRUD_Admin_Column
     */
    public function setType($type) {
        
        switch ($type) {
            case self::TEXT_TYPE:
            case self::EDIT_LINK_TYPE:
            case self::IMAGE_TYPE:
            case self::SWITCH_BUTTON_TYPE:
            case self::CUSTOM_TYPE:
                $this->type = $type;
                break;

            default:
                throw new InvalidArgumentException("type is an invalid value for CRUD Column");
        }
        
        return $this;
    }
    
    /**
     * @return string
     */
    private function getPrefix() {
        return $this->prefix;
    }
    
    /**
     * Nastaví prefix ke každé hodnotě, která se v tabulce vypíše
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param type $unit
     * @return \KT_CRUD_Admin_Column
     */
    public function setPrefix($unit) {
        $this->prefix = $unit;
        return $this;
    }
    
    /**
     * @return string
     */
    private function getSuffix() {
        return $this->suffix;
    }

    /**
     * Nastaví suffix ke každé hodnotě, která se v tabulce vypíše
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param type $unit
     * @return \KT_CRUD_Admin_Column
     */
    public function setSuffix($unit) {
        $this->suffix = $unit;
        return $this;
    }    
    
    /**
     * @return string
     */
    private function getCustomCallbackFunction() {
        return $this->customCallbackFunction;
    }

    /**
     * Nastaví název funkce, která se bude volat při zobrazení obsahu u jednotlivé položky
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param type $customCallbackFunction
     * @return \KT_CRUD_Admin_Column
     */
    public function setCustomCallbackFunction($customCallbackFunction) {
        $this->customCallbackFunction = $customCallbackFunction;
        return $this;
    }
    
    /**
     * @return boolean
     */
    private function getDeleteAble() {
        return $this->deletable;
    }

    /**
     * Nastaví, aby mohl být záznam smazán z pozice výpisu tabulky pomocí Ajaxu
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param type $deleteAble
     * @return \KT_CRUD_Admin_Column
     */
    public function setDeleteAble($deleteAble = true) {
        $this->deletable = $deleteAble;
        return $this;
    }

        
        
    // --- veřejné funkce ------------------
    
    /**
     * Vrátí obsah buňky na základě předaných parametrů a nastaveného typu sloupce
     * 
     * @param string $itemValue
     * @param int $itemId
     * @param string $className
     * @return string
     * @throws InvalidArgumentException
     */
    public function getCellContent( $item ){      
        if(kt_not_isset_or_empty($item)){
            return "";
        }
        
        $columnName = $this->getName();
        $className = get_class($item);
        $itemId = $item->getId();
        $itemValue = $item->$columnName;
        
        switch ($this->getType()) {
            case self::TEXT_TYPE:
                return $html = $this->getTextTypeContent($itemValue);
                
            case self::EDIT_LINK_TYPE:
                return $html = $this->getEditLinkTypeContent($itemValue, $itemId, $className);
            
            case self::IMAGE_TYPE:
                return $html = $this->getImageTypeContent($itemValue);
                
            case self::SWITCH_BUTTON_TYPE:
                return $html = $this->getSwitchButtonTypeContent($itemValue, $itemId, $className);
                
            case self::CUSTOM_TYPE:
                return $html = $this->getCustomTypeContent($itemValue, $itemId, $className);

            default:
                throw new InvalidArgumentException("column type is an invalid value for CRUD Column");
        }
        
    }
    
    // --- privátní funkce ------------------
    
    
    /**
     * Vrátí obsah buňky s obyčejným textovým typem
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param type $itemValue
     * @return type
     */
    private function getTextTypeContent( $itemValue ){
        $html = "";
        
        $html .= $this->getPrefixContent();
        $html .= $itemValue;
        $html .= $this->getSuffixContent();
        
        return $html;
    }
    
    /**
     * Vrátí obsah buňky s možností prokliku do detailu záznamu a případného smazání
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $itemValue
     * @param int $itemId
     * @param string $className
     */
    private function getEditLinkTypeContent($itemValue, $itemId, $className){
        
        $html = "";
        $updateUrl = add_query_arg( array( "action" => "update", $className::ID_COLUMN => $itemId ) );
        
        $html .= "<a href=\"$updateUrl\" class=\"id-link\" title=\"editovat záznam\">$itemValue</a>";
        $html .= "<span class=\"row-actions\">";
        $html .= "<a href=\"$updateUrl\">". __("Detail", KT_DOMAIN) ."</a>";
        
        if($this->getDeleteAble()){
            $html .= "<span class=\"delete-row\" title=\"". __( 'Trvale smazat tento záznam', KT_DOMAIN ) . "\" data-id=\"$itemId\" data-type=\"$className\">". __( "Smazat", KT_DOMAIN ) . "</span>";
        }
        
        $html .= "</span>";
        
        return $html;
    }
    
    /**
     * Vrátí obsah buňky s možnosti nastavení switchfieldu dle zvolené hodnoty
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $itemValue
     * @param int $itemId
     * @param string $className
     * @return string
     */
    private function getSwitchButtonTypeContent($itemValue, $itemId, $className){
        $switchField = new KT_Switch_Field( "kt-crud-switch-list-field-" . $itemId, "");
	$switchField->setValue($itemValue)
            ->addAttribute("data-item-type", $className)
            ->addAttribute("data-item-id", $itemId)
            ->addClass("edit-crud-switch-list-field");
        
        return $html = $switchField->getField();
    }
    
    /**
     * Vrátí obsah buňky s obrázkovým typem
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $itemValue
     * @return string
     */
    private function getImageTypeContent( $itemValue ){
        $html = "";
        
        $attachmentData = wp_get_attachment_image_src($itemValue);
        
        if( ! $attachmentData){
            return $html;
        }
        
        $html .= $this->getPrefixContent();
        $html .= "<img src=\"{$attachmentData[0]}\" width=\"90\" height=\"90\">";
        $html .= $this->getSuffixContent();
        
        return $html;
    }
    
    /**
     * Vytvoří filtr z názvu custom call back funkci a předá do ní všechny parametry.
     * Vrácený obsah z filtru vypíše jako html.
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $itemValue
     * @param int $itemId
     * @param string $className
     * @return html
     */
    private function getCustomTypeContent($itemValue, $itemId, $className){
        $html = "";
        $html .= $this->getPrefixContent();
        $html .= apply_filters($this->getCustomCallbackFunction(), $itemValue, $itemId, $className);
        $html .= $this->getSuffixContent();
        
        return $this;
    }
    
    /**
     * Zkontroluje, zda má sloupec definovaný prefix, pokud ano, tak ho vrátí
     * pokud ne, vrátí prázdný string.
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    private function getPrefixContent(){
        if(kt_isset_and_not_empty($this->getPrefix())){
            return $this->getPrefix();
        }
        
        return "";
    }
    
    /**
     * Zkontroluje, zda má sloupec definovaný suffix, pokud ano, tak ho vrátí
     * pokud ne, vrátí prázdný string
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    private function getSuffixContent(){
        if(kt_isset_and_not_empty($this->getSuffix())){
            return $this->getSuffix();
        }
        
        return "";
    }
    
    






}
