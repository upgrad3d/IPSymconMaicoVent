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

        //generate profiles - todo
        

        //Connect enocean splitter (enocean gateway)
        $this->ConnectParent("{A52FEFE9-7858-4B8E-A96E-26E15CB944F7}");

        //
        $this->RegisterPropertyInteger("deviceId", -1);
        $this->RegisterPropertyString("returnId", "FFFFFFFF");
        
        
        $this->RegisterVariableInteger("fanSpeed", $this->Translate("Fan Speed"));
        $this->RegisterVariableInteger("operatingmode", $this->Translate("Operating Mode"));
        $this->RegisterVariableString("binDeviceId", $this->Translate("Device ID Binary"));


        $this->RegisterPropertyString("BaseData", '
        {
            "DataID":"{70E3075F-A35D-4DEB-AC20-C929A156FE48}",
            "Device":246,
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

        $this->SetValue("binDeviceId", base_convert($this->ReadPropertyString("returnId"), 16, 10));
        

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

    #================================================================================================
    public function ReceiveData($JSONString)
    #================================================================================================
    {
        //$this->SendDebug("Receive", $JSONString, 0);
        $data = json_decode($JSONString);
        $this->SendDebug("JSON", $data, 0);


    }

}