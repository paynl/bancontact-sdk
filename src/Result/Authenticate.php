<?php


namespace Paynl\BancontactSDK\Result;


/**
 * Class GetStatus
 * @package Paynl\BancontactSDK\Result
 *
 * @property-read string $paReq
 * @property-read string $postUrl
 * @property-read string $MD
 */
class Authenticate extends Result
{
    /**
     * Generate a form to 3d secure
     *
     * @param string $returnUrl
     * @return string
     */
    public function get3DSForm(string $returnUrl): string
    {
        $id = uniqid();
        $form = "
    <form id=\"bancontactForm_{$id}\" name=\"bancontactForm_{$id}\" method=\"post\" action=\"{$this->postUrl}\">
        <input type=\"hidden\" name=\"PaReq\" value=\"{$this->paReq}\">
        <input type=\"hidden\" name=\"MD\" value=\"{$this->MD}\">
        <input type=\"hidden\" name=\"TermUrl\" value=\"{$returnUrl}\">
        <input type='submit'>
    </form>
    <script type=\"application/javascript\">
//        document.forms['bancontactForm_{$id}'].submit();
    </script>";
        return $form;

    }
}