<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use TCPDF;
use setasign\Fpdi\Tcpdi;
use Illuminate\Support\Facades\Storage;


class PDFController extends Controller
{

/**
 * @OA\Get(
 * path="/api/generate-pdf-crt",
 * summary="Generate PDF with crt file",
 * description="Generate PDF using crt file",
 * tags={"Genearte PDF With Digitag Signature Using crt File"},
 * @OA\Response(
 * response=200,
 * description="Success",
 * @OA\MediaType(
 * mediaType="application/json",
 * )
 * ),
 * )
 */
    
        public function generatePDFcrt(Request $request){
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // $p12Certificate = storage_path('/JhonnielYgay.crt'); //You can use either storage_path or base_path
            $p12Certificate = 'file://'.base_path().'/storage/JhonnielYgay.crt';

                $info = array(
                    'Name' => 'Kaushal Kushwaha',
                    'Location' => 'Indore',
                    'Reason' => 'I Review This Document',
                    'ContactInfo' => '',
                );
    
            PDF::setSignature($p12Certificate, $p12Certificate, 'pdfWithEsig', '', 2, $info);
    
            PDF::SetFont('helvetica', '', 12);
            PDF::SetCreator('Kaushal Kushwaha');
            PDF::SetTitle('new-pdf');
            PDF::SetAuthor('Kaushal');
            PDF::SetSubject('Generated PDF');
            PDF::AddPage();
            $html = '<div>
                <h1>What is Lorem Ipsum?</h1>
                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                Lorem Ipsum has been the industry`s standard dummy text ever since the 1500s,
                when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                It has survived not only five centuries, but also the leap into electronic typesetting,
                remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset
                sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like
                Aldus PageMaker including versions of Lorem Ipsum.
            </div>';
            PDF::writeHTML($html, true, false, true, false, '');
            $signatureImagePath = storage_path('dummy-sig.png');


            if (!file_exists($signatureImagePath) || !is_readable($signatureImagePath)) {
                die("Cannot access image file: $signatureImagePath");
            }
        
            PDF::Image($signatureImagePath, 50, 150, 40, 15, 'PNG');
            PDF::setSignatureAppearance(50, 150, 40, 15);

            PDF::Output(public_path('PdfWithCrtSig.pdf'), 'F');
            PDF::reset();
            $message = 'PDF Generated Successfully';
            return $message;
        }

/**
 * @OA\Get(
 * path="/api/generate-pdf-p12",
 * summary="Generate PDF with p12 file",
 * description="Generate PDF",
 * tags={"Genearte PDF With Digitag Signature Using P12 File"},
 * @OA\Parameter(
 *    name="p12Password",
 *    in="query",
 *    description="Password for the p12 file",
 *    required=true,
 *    @OA\Schema(
 *        type="string",
 *        example="password"
 *    )
 * ),
 * @OA\Parameter(
 *    name="reason",
 *    in="query",
 *    description="Reason for generating the PDF",
 *    required=true,
 *    @OA\Schema(
 *        type="string",
 *        example="I Review the docs"
 *    )
 * ),
 * @OA\Response(
 * response=200,
 * description="Success",
 * @OA\MediaType(
 * mediaType="application/json",
 * )
 * ),
 * )
 */
    public function generatePDFvery(Request $request) {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $p12Certificate = base_path().'/storage/JhonnielYgay.p12'; //Your Path to the p12 file

        if (!is_readable($p12Certificate)) {
            $message =  'The P12 file is not readable';
            return;
        }

        $p12Password = $request->input('p12Password');



            $p12ReadResult = openssl_pkcs12_read(file_get_contents($p12Certificate), $p12Data, $p12Password);
            if (!$p12ReadResult) {
                $message = 'Incorrect password for the P12 file';
                return $message;
            }

            $certificate = $p12Data['cert'];
            $privateKey = $p12Data['pkey'];

            $reason = $request->input('reason');

            $info = array(
                'Name' => 'Jhan niel',
                'Location' => 'Camiguin PH',
                'Reason' => $reason,
                'ContactInfo' => '',
            );

        PDF::setSignature($certificate, $privateKey, 'pdfWithEsig', '', 2, $info);


        PDF::SetFont('helvetica', '', 12);
        PDF::SetCreator('Kaushal Kushwaha');
        PDF::SetTitle('new-pdf');
        PDF::SetAuthor('Kaushal');
        PDF::SetSubject('Generated PDF');
        PDF::AddPage();
        $html = view('pdf-content')->render();
        PDF::writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


        $signatureImagePath = storage_path('dummy-sig.png');


        if (!file_exists($signatureImagePath) || !is_readable($signatureImagePath)) {
            die("Cannot access image file: $signatureImagePath");
        }
    
        PDF::Image($signatureImagePath, 50, 150, 40, 15, 'PNG');
        PDF::setSignatureAppearance(50, 150, 40, 15);

        PDF::Output(public_path('DumypdfWithP12Esig.pdf'), 'F');
        PDF::reset();
        $message = 'PDF Generated Successfully';
        return $message;
    }

}