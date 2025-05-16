<?php

require_once 'vendor/autoload.php';

use App\Anonymizer;

$text = "Jméno: Holubová
        E-mail: nholub@email.cz
        Tel.: 605075813
        Text:Dobrý den, mám dotaz na ovčí brynzu. Opakovaně se mě stalo, že v řetězci koupím brynzu a zjistím, že obsahuje jen 50% ovčího hrudkového sýra a také kravský hrudkový sýr. Uvádí např. plnotučná, ale obsahuje 48% tuku, např. řetězec Billa nebo Penny. Domnívám se, že jde o klamání spotřebitele a měla by být označena jako \"smíšená\". Cena 125 g v ceně 36 Kč od výrobce Slovensko Zvolenská Slatina. Navíc měla hořkou chuť a byla s 50% slevou.
        Prosím o odpověď. Děkuji Holubová
        p.s. kvalitní brynza se běžně v obchodě těžko koupí";

$anonymizer = new Anonymizer();

$result = $anonymizer->anonymize($text);

echo $result;
