<?php

/* On ajoute un service pour générer les documents PDF */

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private $dompdf;

    public function __construct()
    {
        // Configurer domPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $this->dompdf = new Dompdf($options);
    }

    public function generatePdf(string $htmlContent, string $fileName)
    {
        // Charger le contenu HTML dans domPDF
        $this->dompdf->loadHtml($htmlContent);

        // (Optionnel) Définir la taille du papier
        $this->dompdf->setPaper('A4', 'portrait'); // ou 'landscape'

        // Rendre le PDF
        $this->dompdf->render();

        // Envoyer le PDF directement au navigateur (ou l'enregistrer sur le serveur)
        $this->dompdf->stream($fileName, ["Attachment" => 0]); // 0 pour afficher dans le navigateur, 1 pour forcer le téléchargement
    }
}
