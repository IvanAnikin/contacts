<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#use Twig\Extension\AbstractExtension;

class DefaultController extends AbstractController
{
    public function index(Request $request): Response
    {
        $dir = __DIR__.'/contacts.xml';

        $date = new \DateTime();

        $action = $request->query->get('action'); #$request->request->get('action');

        $flashes=[];

        if($action!=Null){

            $name = $request->query->get('name');
            $surname = $request->query->get('surname');
            $phone = $request->query->get('phone');
            $mail = $request->query->get('mail');
            $note = $request->query->get('note');
            $id = $request->query->get('id');

            if($name==Null || $surname==Null || $mail==Null || ($id==Null && $action == "Delete") || ($id==Null && $action == "Edit")){
                $message="Please fill all required fields.";

                if($name==Null){
                    $message = $message." Name field is required.";
                }
                if($surname==Null){
                    $message = $message." Surname field is required.";
                }
                if($mail==Null){
                    $message = $message." Mail field is required.";
                }

                $flashes[0] = [$message, 'alert-warning'];

                $xml = simplexml_load_file($dir);
                $list = $xml->record;

                return $this->render('Default/index.html.twig', [
                    'contacts' => $list,
                    'page_title' => 'Contacts management',
                    'flashes' => $flashes,
                    'date' => $date->format('Y'),
                ]);
            }

            if($action == "Edit") {
                $xml = simplexml_load_file($dir);

                $contact = $xml->xpath('/contacts/record[id=' . $id . ']');
                $contact[0]->name = $name;
                $contact[0]->surname = $surname;
                $contact[0]->phone = $phone;
                $contact[0]->mail = $mail;
                $contact[0]->note = $note;

                $xml->asXML($dir);

                $flashes[0] = ["Contact edited successfully", 'alert-primary'];
            }
            if($action == "Add") {
                $xml = simplexml_load_file($dir);
                $list = $xml->record;

                $max_id = 0;
                foreach ($list as $contact){
                    if((int)$contact[0]->id>$max_id){
                        $max_id=(int)$contact[0]->id;
                    }
                }

                $record = $xml->addChild("record", "103");
                $record->addChild("name", $name);
                $record->addChild("surname", $surname);
                $record->addChild("phone", $phone);
                $record->addChild("mail", $mail);
                $record->addChild("note", $note);
                $record->addChild("id", $max_id+1);

                $xml->asXML($dir);

                $flashes[0] = ["Contact added successfully", 'alert-primary'];
            }
            if($action == "Delete") {
                $xml = simplexml_load_file($dir);

                $new_id=0;
                while(True){
                    if((int)$xml->record[$new_id][0]->id == (int)$id){
                        unset($xml->record[$new_id][0]);
                        break;
                    }
                    $new_id+=1;
                }

                $xml->asXML($dir);

                $flashes[0] = ["Contact deleted successfully", 'alert-primary'];
            }
        }
        #Warning
        #$flashes[0] = [$action, 'alert-warning'];
        #https://getbootstrap.com/docs/4.0/components/alerts/

        $xml = simplexml_load_file($dir);
        $list = $xml->record;

        return $this->render('Default/index.html.twig', [
            'contacts' => $list,
            'page_title' => 'Contacts management',
            'flashes' => $flashes,
            'date' => $date->format('Y'),
        ]);
    }

}