<?php
interface iData{
    public function nullify($value);
}

class Data implements iData
{
    public $fecha, $nPaquete, $unicast, $multicast, $broadcast, $arp, $aaaa, $icmp, $udp, $tcp, $otros, $ipv6, $bbbb, $cccc, $ssdp, $icpmv6;

    function __construct($fecha, $nPaquete, $uni, $multi, $broad, $arp, $traffic, $icmp, $udp, $tcp, $resto, $ipv6, $arp46, $badip, $ssdp, $icmp6)
    {
        $this->fecha     = $fecha;
        $this->nPaquete  = $nPaquete;
        $this->unicast   = $this->nullify($uni);
        $this->multicast = $this->nullify($multi);
        $this->broadcast = $this->nullify($broad);
        $this->arp       = $this->nullify($arp);
        $this->aaaa      = $this->nullify($traffic);
        $this->icmp      = $this->nullify($icmp);
        $this->udp       = $this->nullify($udp);
        $this->tcp       = $this->nullify($tcp);
        $this->otros     = $this->nullify($resto);
        $this->ipv6      = $this->nullify($ipv6);
        $this->bbbb      = $this->nullify($arp46);
        $this->cccc      = $this->nullify($badip);
        $this->ssdp      = $this->nullify($ssdp);
        $this->icpmv6    = $this->nullify($icmp6);
        
    }
    
    public function nullify($value){
        return $value == 0? null : $value;
    }
}
?>