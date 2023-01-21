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

        //register variables for operation 
        $this->RegisterVariableInteger("fanSpeed", $this->Translate("Fan Speed"));
        $this->RegisterVariableInteger("operatingmode", $this->Translate("Operating Mode"));

        //generate profiles - todo
        

        //Connect enocean splitter (enocean gateway)
        $this->ConnectParent("{A52FEFE9-7858-4B8E-A96E-26E15CB944F7}");

        //
        $this->RegisterPropertyInteger("DeviceID", -1);
		$this->RegisterPropertyString("ReturnID", "");


        $this->RegisterPropertyString("BaseData", '
        {
            "DataID":"{DE2DA2C0-7A28-4D23A9AA6D1C7609C7EC}",
            "Device":0xF6,
            "Status":0,
            "DeviceID":21,
            "DestinationID":0,
            "DataLength":4,
            "DataByte12":0,
            "DataByte11":0,
            "DataByte10":0,
            "DataByte9":0,
            "DataByte8":0,
            "DataByte7":0,
            "DataByte6":0,
            "DataByte5":0,
            "DataByte4":0,
            "DataByte3":0,
            "DataByte2":0,
            "DataByte1":0,
            "DataByte0":48
        }');
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
            case "Test":
                $this->test();
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


    #================================================================================================
    protected function SendDebug($Message, $Data, $Format)
    #================================================================================================
    {
        if (is_array($Data))
        {
            foreach ($Data as $Key => $DebugData)
            {
                    $this->SendDebug($Message . ":" . $Key, $DebugData, 0);
            }
        }
        else if (is_object($Data))
        {
            foreach ($Data as $Key => $DebugData)
            {
                    $this->SendDebug($Message . "." . $Key, $DebugData, 0);
            }
        }
        else
        {
            parent::SendDebug($Message, $Data, $Format);
        }
    } 
    
    public function test()
    #================================================================================================
    {
        $data = json_decode($this->ReadPropertyString("BaseData"));
        $data->DeviceID = $this->ReadPropertyInteger("DeviceID");
        $this->SendDataToParent(json_encode($data));
        //$this->SendDebug("Transmit", $data, 0);
    }
}