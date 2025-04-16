<?php

/* On ajoute un service pour générer les documents PDF */

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private Dompdf $dompdf; 
    // on stocke l'instance dans une propriété, préalablement déclarée (2)

    public function __construct()
    {
        // Configurer domPDF : on active diverses options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true); // pour charger les images avec chemins absolus
        $options->set('defaultFont', 'Arial');

        $this->dompdf = new Dompdf($options); 
        // on initialise DomPDF dans le constructeur (1) : 
        // on stocke dans l'objet courant ($this) une nouvelle instance de Dompdf
    }

    public function getPdfContent(string $htmlContent): string
    {
        // Charger le contenu HTML dans domPDF
        $this->dompdf->loadHtml($htmlContent);

        // (Optionnel) Définir la taille du papier
        $this->dompdf->setPaper('A4', 'portrait'); // ou 'landscape'

        // Rendre le PDF
        $this->dompdf->render();

        // option pour retourner le contenu du PDF
        return $this->dompdf->output();
    }
}



