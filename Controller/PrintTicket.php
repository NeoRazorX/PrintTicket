<?php
/**
 * This file is part of PrintTicket plugin for FacturaScripts
 * Copyright (C) 2018-2019 Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\PrintTicket\Controller;

use FacturaScripts\Core\Base\Controller;
use FacturaScripts\Dinamic\Lib\BusinessDocumentTicket;
use FacturaScripts\Dinamic\Model\Ticket;

/**
 * Controller to generate a receipt from BusinessDocument Model.
 *
 * @author Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
 */
class PrintTicket extends Controller
{
    public $businessDocument;

    public function getPageData()
    {
        $pageData = parent::getPageData();
        $pageData['title'] = 'Configuracion Tickets';
        $pageData['menu'] = 'admin';
        $pageData['icon'] = 'fas fa-print';
        $pageData['showonmenu'] = false;

        return $pageData;
    }

    public function privateCore(&$response, $user, $permissions)
    {
        parent::privateCore($response, $user, $permissions);
        $this->setTemplate('PrintTicketScreen');

        $code = $this->request->query->get('code');
        $modelName = $this->request->query->get('documento');        

        if ('' === $modelName || '' === $code) {
            return;
        }
        
        $className = 'FacturaScripts\\Dinamic\\Model\\' . $modelName;
        
        $this->businessDocument = (new $className)->get($code);
        if ($this->businessDocument) {
            $this->savePrintJob($this->businessDocument);
        }
    }

    private function savePrintJob($document)
    {
        $businessTicket = new BusinessDocumentTicket($document); 

        $ticket = new Ticket();
        $ticket->coddocument = $document->modelClassName();
        $ticket->text = $businessTicket->getTicket();  

        //echo $businessTicket->getTicket();   

        if (!$ticket->save()) {
            echo 'Error al guardar el ticket';
        }
    }
}
