<?php
/**
 * Part of the ETD Framework Pagination Package
 *
 * @copyright   Copyright (C) 2015 ETD Solutions, SARL Etudoo. Tous droits réservés.
 * @license     Apache License 2.0; see LICENSE
 * @author      ETD Solutions http://etd-solutions.com
 */

namespace EtdSolutions\Pagination;

use EtdSolutions\Language\LanguageFactory;
use Joomla\Data\DataObject;

class Pagination extends DataObject {

    /**
     * Instancie l'objet Pagination.
     *
     * @param   int $total Le nombre total d'éléments.
     * @param   int $start Le décalage du premier élément.
     * @param   int $limit Le nombre d'éléments à afficher par page.
     */
    public function __construct($total, $start, $limit) {

        // Initialisation.
        $viewall      = false;
        $total        = (int)$total;
        $start        = (int)max($start, 0);
        $limit        = (int)max($limit, 0);
        $pagesCurrent = 0;
        $pagesTotal   = 0;

        if ($limit > $total) {
            $start = 0;
        }

        if (!$limit) {
            $limit = $total;
            $start = 0;
        }

        /*
		 * Si start est plus grand que le total (i.e. on demande à afficher des enregistrments qui n'existent pas)
		 * alors on définit start pour afficher la dernière page naturelle des enregistrements
		 */
        if ($start > $total - $limit) {
            $start = max(0, (int)(ceil($total / $limit) - 1) * $limit);
        }

        // On définit le nombre total de pages et les valeurs de la page courante.
        if ($limit > 0) {
            $pagesTotal   = ceil($total / $limit);
            $pagesCurrent = ceil(($start + 1) / $limit);
        }

        // On définit les valeurs de la boucle d'itération de la pagination.
        $displayedPages = 10;
        $pagesStart     = $pagesCurrent - ($displayedPages / 2);

        if ($pagesStart < 1) {
            $pagesStart = 1;
        }

        if ($pagesStart + $displayedPages > $pagesTotal) {
            $pagesStop = $pagesTotal;

            if ($pagesTotal < $displayedPages) {
                $pagesStart = 1;
            } else {
                $pagesStart = $pagesTotal - $displayedPages + 1;
            }
        } else {
            $pagesStop = $pagesStart + $displayedPages - 1;
        }

        // Si on affiche tous les enregistrements, on passe le drapeau à true.
        if ($limit == 0) {
            $viewall = true;
        }

        parent::__construct(array(
            'viewall'        => $viewall,
            'total'          => $total,
            'start'          => $start,
            'limit'          => $limit,
            'pagesTotal'     => $pagesTotal,
            'pagesCurrent'   => $pagesCurrent,
            'pagesStart'     => $pagesStart,
            'pagesStop'      => $pagesStop,
            'displayedPages' => $displayedPages
        ));

    }

    /**
     * Create and return the pagination pages counter string, ie. Page 2 of 4.
     *
     * @return  string   Pagination pages counter string.
     */
    public function getPagesCounter() {

        $text = (new LanguageFactory())->getText();
        $html = null;

        if ($this->getProperty('pagesTotal') > 1) {
            $html .= $text->sprintf('APP_PAGINATION_HTML_PAGE_CURRENT_OF_TOTAL', $this->getProperty('pagesCurrent'), $this->getProperty('pagesTotal'));
        }

        return $html;
    }

}