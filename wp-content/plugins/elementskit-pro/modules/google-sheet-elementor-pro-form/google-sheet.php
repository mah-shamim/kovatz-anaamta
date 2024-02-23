<?php

namespace ElementsKit\Modules\Google_Sheet_Elementor_Pro_Form;

use ElementskitVendor\Google\Client as Google_Client;
use ElementskitVendor\Google\Service\Sheets as Google_Service_Sheets;
use ElementskitVendor\Google\Service\Sheets\Spreadsheet as Google_Service_Sheets_Spreadsheet;
use ElementskitVendor\Google\Service\Sheets\ValueRange as Google_Service_Sheets_ValueRange;

defined('ABSPATH') || exit;

class Google_Sheet
{
    const ACCESS_TOKEN_KEY = 'ekits_google_sheet_access_token';

    /**
     * @var mixed
     */
    private $access_token;

    /**
     * @var mixed
     */
    private $admin_settings;

    /**
     * @var mixed
     */
    private $form;

    /**
     * @var mixed
     */
    private $spreadsheet_id;

    /**
     * @param $form
     * @param $admin_settings
     */
    public function __construct($form, $admin_settings)
    {
        $this->form           = $form;
        $this->admin_settings = $admin_settings;
        $get_access_token     = get_option(self::ACCESS_TOKEN_KEY);
        $this->access_token   = json_decode($get_access_token, true);
    }

    /**
     * @return mixed
     */
    public function insert()
    {
        if (empty($this->access_token['access_token'])) {
            return false;
        }
        $spreadsheet = $this->has_spreadsheet();
        if ($spreadsheet) {
            return $this->send_form_data();
        }
    }

    /**
     * @return mixed
     */
    public function send_form_data()
    {
        try {
            $values       = array_values($this->form['data']);
            $column_range = 'A1:Z1';
            $body         = new Google_Service_Sheets_ValueRange([
                'values' => [$values]
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            return $this->google_service_sheets()->spreadsheets_values->append($this->spreadsheet_id, $column_range, $body, $params);
        } catch (\Exception $e) {
            if (401 == $e->getCode() && $this->refresh_access_token()) {
                return $this->send_form_data();
            }
        }
    }

    /**
     * @return mixed
     */
    public function create_sheet()
    {
        try {
            $spreadsheet = new Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => $this->form['name']
                ]
            ]);
            $spreadsheet = $this->google_service_sheets()->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId'
            ]);
            $this->spreadsheet_id = $spreadsheet->spreadsheetId;
            $this->insert_sheet_column_names();
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function insert_sheet_column_names()
    {
        try {
            $range = 'A1:Z1';
            $body  = new Google_Service_Sheets_ValueRange([
                'values' => [array_keys($this->form['data'])]
            ]);
            $params = [
                'valueInputOption' => 'USER_ENTERED'
            ];
            $this->google_service_sheets()->spreadsheets_values->update($this->spreadsheet_id, $range, $body, $params);

        } catch (\Exception $e) {
            if (401 == $e->getCode() && $this->refresh_access_token()) {
                $this->insert_sheet_column_names();
            }
        }
    }

    /**
     * @param $form_id
     * @param $names
     * @param $title
     * @return mixed
     */
    public function has_spreadsheet()
    {
        $form_sheet_id_key    = 'ekit_google_sheet_' . $this->form['id'];
        $this->spreadsheet_id = get_option($form_sheet_id_key);

        if (!$this->spreadsheet_id) {
            $create_sheet = $this->create_sheet();
            if (!$create_sheet) {
                return false;
            }
            update_option($form_sheet_id_key, $this->spreadsheet_id);

        } else {
            $sheet = $this->is_sheet_exist();
            if (!$sheet) {
                $create_sheet = $this->create_sheet();
                if (!$create_sheet) {
                    return false;
                }
                update_option($form_sheet_id_key, $this->spreadsheet_id);
            }
        }
        return true;
    }

    public function is_sheet_exist()
    {
        try {
            $this->google_service_sheets()->spreadsheets->get($this->spreadsheet_id);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refresh_access_token()
    {
        $response = wp_remote_post('https://accounts.google.com/o/oauth2/token', [
            'body' => [
                'grant_type'    => "refresh_token",
                'refresh_token' => $this->access_token['refresh_token'],
                'client_id'     => $this->admin_settings['google']['client_id'],
                'client_secret' => $this->admin_settings['google']['client_secret']
            ]
        ]);

        if (200 === wp_remote_retrieve_response_code($response)) {
            $response_body                  = json_decode(wp_remote_retrieve_body($response), true);
            $response_body['refresh_token'] = $this->access_token['refresh_token'];
            update_option(self::ACCESS_TOKEN_KEY, json_encode($response_body));
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function google_service_sheets()
    {
        $google_client = new Google_Client();
        $accessToken   = [
            'access_token' => $this->access_token['access_token'],
            'expires_in'   => $this->access_token['expires_in']
        ];
        $google_client->setAccessToken($accessToken);
        $google_service = new Google_Service_Sheets($google_client);
        return $google_service;
    }
}
