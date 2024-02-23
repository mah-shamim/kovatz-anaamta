<?php
if ( ! defined( 'ABSPATH' ) ) {
    return;
}
/**
 * Shipping providers class.
 *
 * @class   WCV_Shipping_Providers
 *
 * @version 2.4.8
 * @since   2.4.8 - Added
 */
class WCV_Shipping_Providers {
    /**
     * Get shipping providers.
     *
     * @return array
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_providers() {
        return apply_filters(
            'wcv_shipping_providers',
            array(
                'Australia'      => array(
                    'Australia Post'   => 'https://auspost.com.au/mypost/track/#/details/%1$s',
                    'Fastway Couriers' => 'https://www.fastway.com.au/tools/track/?l=%1$s',
                ),
                'Austria'        => array(
                    'post.at' => 'https://www.post.at/sv/sendungsdetails?snr=%1$s',
                    'dhl.at'  => 'https://www.dhl.at/content/at/de/express/sendungsverfolgung.html?brand=DHL&AWB=%1$s',
                    'DPD.at'  => 'https://tracking.dpd.de/parcelstatus?locale=de_AT&query=%1$s',
                ),
                'Brazil'         => array(
                    'Correios' => 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=%1$s',
                ),
                'Belgium'        => array(
                    'bpost' => 'https://track.bpost.be/btr/web/#/search?itemCode=%1$s',
                ),
                'Canada'         => array(
                    'Canada Post' => 'https://www.canadapost-postescanada.ca/track-reperage/en#/resultList?searchFor=%1$s',
                    'Purolator'   => 'https://www.purolator.com/purolator/ship-track/tracking-summary.page?pin=%1$s',
                ),
                'Czech Republic' => array(
                    'PPL.cz'      => 'https://www.ppl.cz/main2.aspx?cls=Package&idSearch=%1$s',
                    'Česká pošta' => 'https://www.postaonline.cz/trackandtrace/-/zasilka/cislo?parcelNumbers=%1$s',
                    'DHL.cz'      => 'https://www.dhl.cz/cs/express/sledovani_zasilek.html?AWB=%1$s',
                    'DPD.cz'      => 'https://tracking.dpd.de/parcelstatus?locale=cs_CZ&query=%1$s',
                ),
                'Finland'        => array(
                    'Itella' => 'https://www.posti.fi/itemtracking/posti/search_by_shipment_id?lang=en&ShipmentId=%1$s',
                ),
                'France'         => array(
                    'Colissimo' => 'https://www.laposte.fr/outils/suivre-vos-envois?code=%1$s',
                ),
                'Germany'        => array(
                    'DHL Intraship (DE)' => 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?lang=de&idc=%1$s&rfn=&extendedSearch=true',
                    'Hermes'             => 'https://www.myhermes.de/empfangen/sendungsverfolgung/sendungsinformation/#%1$s',
                    'Deutsche Post DHL'  => 'https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?lang=de&idc=%1$s',
                    'UPS Germany'        => 'https://wwwapps.ups.com/WebTracking?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=de_DE&InquiryNumber1=%1$s',
                    'DPD.de'             => 'https://tracking.dpd.de/parcelstatus?query=%1$s&locale=en_DE',
                ),
                'Ireland'        => array(
                    'DPD.ie'  => 'https://dpd.ie/tracking?deviceType=5&consignmentNumber=%1$s',
                    'An Post' => 'https://track.anpost.ie/TrackingResults.aspx?rtt=1&items=%1$s',
                ),
                'Italy'          => array(
                    'BRT (Bartolini)' => 'https://as777.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&Nspediz=%1$s',
                    'DHL Express'     => 'https://www.dhl.it/it/express/ricerca.html?AWB=%1$s&brand=DHL',
                ),
                'India'          => array(
                    'DTDC' => 'https://www.dtdc.in/tracking/tracking_results.asp?Ttype=awb_no&strCnno=%1$s&TrkType2=awb_no',
                ),
                'Netherlands'    => array(
                    'PostNL'          => 'https://postnl.nl/tracktrace/?B=%1$s&P=%2$s&D=%3$s&T=C',
                    'DPD.NL'          => 'https://tracking.dpd.de/status/en_US/parcel/%1$s',
                    'UPS Netherlands' => 'https://wwwapps.ups.com/WebTracking?sort_by=status&tracknums_displayed=1&TypeOfInquiryNumber=T&loc=nl_NL&InquiryNumber1=%1$s',
                ),
                'New Zealand'    => array(
                    'Courier Post' => 'https://trackandtrace.courierpost.co.nz/Search/%1$s',
                    'NZ Post'      => 'https://www.nzpost.co.nz/tools/tracking?trackid=%1$s',
                    'Aramex'       => 'https://www.aramex.co.nz/tools/track?l=%1$s',
                    'PBT Couriers' => 'http://www.pbt.com/nick/results.cfm?ticketNo=%1$s',
                ),
                'Poland'         => array(
                    'InPost'        => 'https://inpost.pl/sledzenie-przesylek?number=%1$s',
                    'DPD.PL'        => 'https://tracktrace.dpd.com.pl/parcelDetails?p1=%1$s',
                    'Poczta Polska' => 'https://emonitoring.poczta-polska.pl/?numer=%1$s',
                ),
                'Romania'        => array(
                    'Fan Courier'   => 'https://www.fancourier.ro/awb-tracking/?xawb=%1$s',
                    'DPD Romania'   => 'https://tracking.dpd.de/parcelstatus?query=%1$s&locale=ro_RO',
                    'Urgent Cargus' => 'https://app.urgentcargus.ro/Private/Tracking.aspx?CodBara=%1$s',
                ),
                'South Africa'   => array(
                    'SAPO'    => 'http://sms.postoffice.co.za/TrackingParcels/Parcel.aspx?id=%1$s',
                    'Fastway' => 'https://fastway.co.za/our-services/track-your-parcel?l=%1$s',
                ),
                'Sweden'         => array(
                    'PostNord Sverige AB' => 'https://portal.postnord.com/tracking/details/%1$s',
                    'DHL.se'              => 'https://www.dhl.com/se-sv/home/tracking.html?submit=1&tracking-id=%1$s',
                    'Bring.se'            => 'https://tracking.bring.se/tracking/%1$s',
                    'UPS.se'              => 'https://www.ups.com/track?loc=sv_SE&tracknum=%1$s&requester=WT/',
                    'DB Schenker'         => 'http://privpakportal.schenker.nu/TrackAndTrace/packagesearch.aspx?packageId=%1$s',
                ),
                'United Kingdom' => array(
                    'DHL'                       => 'https://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB=%1$s',
                    'DPD.co.uk'                 => 'https://www.dpd.co.uk/apps/tracking/?reference=%1$s#results',
                    'EVRi'                      => 'https://www.evri.com/track/parcel/%1$s',
                    'EVRi (international)'      => 'https://international.evri.com/tracking/%1$s',
                    'InterLink'                 => 'https://www.dpdlocal.co.uk/apps/tracking/?reference=%1$s&postcode=%2$s#results',
                    'ParcelForce'               => 'https://www.parcelforce.com/track-trace?trackNumber=%1$s',
                    'Royal Mail'                => 'https://www3.royalmail.com/track-your-item#/tracking-results/%1$s',
                    'TNT Express (consignment)' => 'https://www.tnt.com/express/en_gb/site/shipping-tools/tracking.html?searchType=con&cons=%1$s',
                    'TNT Express (reference)'   => 'https://www.tnt.com/express/en_gb/site/shipping-tools/tracking.html?searchType=ref&cons=%1$s',
                    'DHL Parcel UK'             => 'https://track.dhlparcel.co.uk/?con=%1$s',
                ),
                'United States'  => array(
                    'DHL US'        => 'https://www.logistics.dhl/us-en/home/tracking/tracking-ecommerce.html?tracking-id=%1$s',
                    'Fedex'         => 'https://www.fedex.com/apps/fedextrack/?action=track&action=track&tracknumbers=%1$s',
                    'FedEx Sameday' => 'https://www.fedexsameday.com/fdx_dotracking_ua.aspx?tracknum=%1$s',
                    'GlobalPost'    => 'https://www.goglobalpost.com/track-detail/?t=%1$s',
                    'OnTrac'        => 'http://www.ontrac.com/trackingdetail.asp?tracking=%1$s',
                    'UPS'           => 'https://www.ups.com/track?loc=en_US&tracknum=%1$s',
                    'USPS'          => 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=%1$s',
                ),
            )
        );
    }

    /**
     * Get providers by country name
     *
     * @param string $country_name The name of the country.
     * @return array
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_providers_by_country( $country_name ) {
        $providers = $this->get_providers();

        $country_name = ucfirst( $country_name );

        return isset( $providers[ $country_name ] ) ? $providers[ $country_name ] : array();
    }

    /**
     * Get all providers and their urls
     *
     * @return array
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_provider_url_list() {
        $providers = $this->get_providers();

        $provider_array = array();
        foreach ( $providers as $all_providers ) {
            foreach ( $all_providers as $provider => $format ) {
                $provider_array[ sanitize_title( $provider ) ] = rawurlencode( $format );
            }
        }

        return $provider_array;
    }
}
