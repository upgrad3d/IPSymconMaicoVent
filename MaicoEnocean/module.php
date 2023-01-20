<?php

class MaicoEnocean extends IPSModule {
    /**
    * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
    * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
    *
    * ABC_MeineErsteEigeneFunktion($id);
    *
    */
    #================================================================================================
    public function Create() 
    #================================================================================================
    {
        //Never delete this line!
        parent::Create();

        //hier sollten Statusvariablen und Modul-Eigenschaften erstellt werden, die das Modul dauerhaft braucht.
        $this->RegisterAttributeBoolean("CurrentState", true);
        
        //Connect enocean splitter (enocean gateway)
        $this->ConnectParent("{A52FEFE9-7858-4B8E-A96E-26E15CB944F7}");
    }

    #================================================================================================
    public function Destroy()
    #================================================================================================
    {
        //Never delete this line!
        parent::Destroy();

    }

    #================================================================================================
    public function ApplyChanges()
    #================================================================================================
    {
        //Never delete this line!
        parent::ApplyChanges();
    }

    #================================================================================================
    public function RequestAction($Ident, $Value)
    #================================================================================================
    {
        switch($Ident) {
            case "FreeDeviceID":
                $this->UpdateFormField('DeviceID', 'value', $this->FreeDeviceID());
                break;
            default:
                throw new Exception("Invalid Ident");
        }
    }

    #================================================================================================
    protected function FreeDeviceID()
    #================================================================================================
    {
        $Gateway = @IPS_GetInstance($this->InstanceID)["ConnectionID"];
        if($Gateway == 0) return;
        $Devices = IPS_GetInstanceListByModuleType(3);             # alle Geräte
        $DeviceArray = array();
        foreach ($Devices as $Device){
            if(IPS_GetInstance($Device)["ConnectionID"] == $Gateway){
                $config = json_decode(IPS_GetConfiguration($Device));
                if(!property_exists($config, 'DeviceID'))continue;
                if(is_integer($config->DeviceID)) $DeviceArray[] = $config->DeviceID;
            }
        }
    
        for($ID = 1; $ID<=256; $ID++)if(!in_array($ID,$DeviceArray))break;
        return $ID == 256?0:$ID;
    }
}