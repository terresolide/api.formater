<?php
$file = __DIR__."/data/indice_aa47.json";
$now = new DateTime();
$indice = array(
        "id" => null,
        "metadataLastUpdate" => $now->format("Y-m-d"),
        "dataLastUpdate" => "",
        "title" => array(
                "fr" => "Indice géomagnétique aa",
                "en" => "aa geomagnetic index"
        ),
        "abstract"=> array(
                "fr" => "L'indice aa mesure les perturbations géomagnétiques horizontales dans la zone équatoriale",
                "en" => "Aa index measure the equatorial index horizontal component disturbances"
        ),
        "description" => array(
                "fr" => "L'indice aa permet de mesurer l'amplitude de l'activité géomagnétique globale pendant des intervalles de trois heures normalisés à une latitude géomagnétique de ± 50 °. aa a été introduit pour surveiller l'activité géomagnétique sur la période de temps la plus longue possible.",
                "en" => "The aa purpose is to measure the amplitude of global geomagnetic activity during 3-hour intervals normalized to geomagnetic latitude ±50°. aa was introduced to monitor geomagnetic activity over the longest possible time period."
        ),
        "observedProperty" => array(
                "name" => "aa INDEX",
                "shortName" => "aa",
                "type" => "time series",
                "timeResolution" => array( "hour", "day"),
                "unit" => "nT"
        ),
        "domainOfInterest" => array( "GEOMAGNETISM"),
        "keywords" => array(array(
                "codeSpace" => "GMD",
                "code" => "5fd5ccc2-5edb-4823-940d-03a290a5c5fc")),
        "pole"  => "formater",
        "status"=> "public",
        "formaterDataCenter" => array(
                "code" => "TS-ANO-4/SNO2",
                "name" => "ISGI"
        ),
        "formats"=> array(  array("name" => "IAGA2002")),
        "processingLevel" => "L4",
        "licence"=> array(
                "code" =>	"CC BY-NC 4.0",
                "url" =>	"https://creativecommons.org/licenses/by-nc/4.0/"
        ),
        "temporalExtents" => array(
                "start" => "1868-01-01",
                "end"   => "now"
        ),
        "procedure"=> array(
                "method"=> array(
                        "fr" => "aa est dérivé des indices K mesurés sur 2 observatoires antipodaux. Les indices K sont converties en amplitudes en utilisant des amplitudes de classe moyenne (K2aK) puis moyennées avec des facteurs de pondération qui tiennent compte de légères variations d'intensité des perturbations géomagnétiques entre aa-observatoires du Nord et du Sud successifs.",
                        "en" => "aa is derived from the K indices measured at two antipodal observatories. The K indices are converted into amplitudes using mid-class amplitudes (K2aK) then averaged with weighting factors that account for slight changes in geomagnetic disturbance intensities between successive Northern and Southern aa-observatories."
                )
        ),
        "quicklook" => array(
                array( "url" => "https://api.poleterresolide.fr/images/isgi/reseau_aa.jpg")
        ),
        "links" => array(
                array(
                        "type" => "INFORMATION_LINK",
                        "url"  => "http://isgi.unistra.fr/indices_aa.php",
                        "description" => array(
                                "fr" => "page de l'indice",
                                "en" => "Index page"
                        )
                ),
                array(
                        "type" => "HTTP_DOWNLOAD_LINK",
                        "url" => "http://isgi.unistra.fr/data_download.php"
                )
                
        )
    );
file_put_contents( $file, json_encode($indice, JSON_NUMERIC_CHECK));
header("Content-Type: application/json");
echo json_encode($indice, JSON_NUMERIC_CHECK);