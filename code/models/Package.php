<?php
class CheckfrontPackageModel extends CheckfrontModel {
    private static $db = array(
        'ItemID' => 'Int',
        'SKU' => 'Varchar(32)',
        'Title' => 'Varchar(32)',
        'Summary' => 'Text',
        'Content' => 'HTMLText',
        'Unit' => 'enum("Day,Hour,Week")',
        'RateSummaryTitle' => 'Varchar(32)',
        'RateSlip' => 'CheckfrontSlip',
        'RateStatus' => 'Varchar(32)',
        'StartDate' => 'SS_DateTime',
        'EndDate' => 'SS_DateTime'
    );
    private static $checkfront_map = array(
        'response' => array(
            'item_id' => 'ItemID',
            'sku' => 'SKU',
            'name' => 'Title',
            'summary' => 'Summary',
            'details' => 'Content',
            'unit' => 'Unit',
            'rate.summary.title' => 'RateSummaryTitle',
            'rate.slip' => 'RateSlip',
            'rate.status' => 'RateStatus'
        ),
        'booking/session' => array(
            'RateSlip' => 'slip'
        )
    );

    /**
     * Make a public link/token to this booking
     *
     * @param $linkType
     *
     * @return String
     */
    public function PublicLink($linkType = CheckfrontModule::LinkTypePublic) {
        return CheckfrontModule::make_link(
            null,
            $this->ItemID,
            null,
            $linkType,
            CheckfrontModule::UserTypeIndividual,
            CheckfrontModule::PaymentPayNow
        );
    }
}