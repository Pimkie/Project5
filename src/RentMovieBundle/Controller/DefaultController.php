<?php

namespace RentMovieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use RentMovieBundle\Entity\Client;
use RentMovieBundle\Entity\Payment;
use RentMovieBundle\Entity\Orders;
use RentMovieBundle\Entity\Movies;
use RentMovieBundle\Models\Logout;

class DefaultController extends Controller
{
	private $mid;
    public function mainAction(Request $request)
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		
		if($request->getMethod()=='POST'){
			$session->clear();
		
			$username=$request->get('login');
			$password=$request->get('password');
			
			$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
			if($userr){
			$login=new Logout();
			$login->setUsername($username);
			$login->setPassword($password);
			$session->set('login',$login);
			/*	$session = $this->getRequest()->getSession();
			$session->set('foo', $userr->getName());
			$session->save();*/
				return $this->render('RentMovieBundle:Default:index.html.twig', array('name'=>$userr->getName()));
			}
		}
		else{
			if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:index.html.twig', array('name'=>$userr->getName()));
				}
			}
			return $this->render('RentMovieBundle:Default:index.html.twig');
		}
	}
	public function registrationAction(Request $request){
		if($request->getMethod()=='POST'){
			$username=$request->get('login');
			$password=$request->get('password');
			$name=$request->get('name');
			$surname=$request->get('surname');
			$email=$request->get('email');
			$pesel=$request->get('pesel');
			
			$con=pg_connect("host=sbazy user=s175520 dbname=s175520 password=pfqrfbhf4");
			$q="Select login from client where login='$username'";
			$r=pg_exec($con,$q);
			if (pg_num_rows($r)>0)
			{
				echo "<script type='text/javascript'>alert('Login name already exist!');</script>";
					return $this->render('RentMovieBundle:Default:registration.html.twig');
			}
			else{
			/*$q="insert into client values('$username','$password','$name','$surname','$email','$pesel')";
			$r=pg_exec($con,$q);*/
			
			$user = new Client();
			$user->setLogin($username);
			$user->setPassword($password);
			$user->setName($name);
			$user->setSurname($surname);
			$user->setEmail($email);
			$user->setPesel($pesel);
			
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($user);
			$em->flush();
			return $this->render('RentMovieBundle:Default:index.html.twig');}
		}
		else {
			return $this->render('RentMovieBundle:Default:registration.html.twig');
		}
	}
	public function logoutAction(Request $request){
		$session=$this->getRequest()->getSession();
		$session->clear();
		return $this->render('RentMovieBundle:Default:index.html.twig');
	}
	public function borrowAction(){			
		return $this->render('RentMovieBundle:Default:cantBorrow.html.twig');
	}
	public function mailAction(Request $request){
			$session=$this->getRequest()->getSession();
			$em = $this->getDoctrine()->getEntityManager();
			$repository = $em->getRepository('RentMovieBundle:Client');
			if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				
				$con=pg_connect("host=sbazy user=s175520 dbname=s175520 password=pfqrfbhf4");
				$q="Select email from client where login='$username'";
				$r=pg_exec($con,$q);
				$val = pg_fetch_result($r, 0, 0);
				
				$radio=$request->get('optionsRadios');
				$term=$request->get('term');
				$date=$request->get('date');
				$month=$request->get('month');
				$year=$request->get('year');
				
				
				if($radio=='option1')
					$rb='cash';
				else if($radio=='option2')
					$rb='credit card';
				$p1=$year."-".$month."-".$date;
				$pd=\DateTime::createFromFormat('Y-m-d', $p1);
				
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				$repo = $em->getRepository('RentMovieBundle:Payment');
			
				$payment = new Payment();
				$payment->setForm($rb);
				$payment->setTerm($term);
				$payment->setPaymentdate($pd);
			
				$em->persist($payment);
				$em->flush();
				
				$pid = $payment->getPaymentid();
			
				$a=$_SERVER['HTTP_REFERER'];
				$tokens = explode('/', $a);
				$mid = $tokens[sizeof($tokens)-1];
				$m=(int)$mid;
				
				$q="Select clientID from client where login='$username'";
				$r=pg_exec($con,$q);
				$cid = pg_fetch_result($r, 0, 0);
				$c=(int)$cid;
				
				$con=pg_connect("host=sbazy user=s175520 dbname=s175520 password=pfqrfbhf4");
				$query = "INSERT INTO orders(clientID, movieID, paymentID) VALUES ($c, $m, $pid);";
				$r=pg_exec($con,$query);
			
			$url = 'https://mandrillapp.com/api/1.0/messages/send.json';
        	$params = [
            'message' => array(
                'subject' => 'Rent Movie: Information according payment',
                'text' => "Form of payment: ".$rb.". Term of payment: ".$term.". Date of payment: ".$p1,
                'html' => '<p>'."Form of payment: ".$rb.". Term of payment: ".$term.". Date of payment: ".$p1.'</p>',
                'from_email' => 'uek@no-replay.com',
                'to' => array(
						array(
							'email' => $val,
							'name' => 'Admin'
							)
						)
				)
			];

				$params['key'] = 'HEpZLrPrRBEa7W9fLAJKeQ';
				$params = json_encode($params);
				$ch = curl_init(); 

				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

				$head = curl_exec($ch); 
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
				curl_close($ch); 
				//echo "<script type='text/javascript'>alert('Sending e-mail to address: $val.');</script>";
			return $this->render('RentMovieBundle:Default:mail.html.twig', array('name'=>$userr->getName()));
		}
		else{
		return $this->render('RentMovieBundle:Default:mail.html.twig');
		}
	}
	public function ordersAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				
				$con=pg_connect("host=sbazy user=s175520 dbname=s175520 password=pfqrfbhf4");
				$q="select orderID,title,genre,paymentID,paymentDate from ((client right join orders using(clientID))left join movies using(movieID))right join payment using(paymentID) where login like '$username';";
				$r=pg_exec($con,$q);
				$rn=pg_numrows($r);
				$cn=pg_numfields($r);
				
				print "<h2 align=\"center\">List of ordered movies:</h2><br/>";
				if ($rn>0)
				{
					print "<table class=\"table table-hover\">";

					print "<th>Order ID<th>Movie's title<th>Movie's genre<th>Payment ID<th>Status";

					for ($j=0;$j<$rn;$j++)
					{
						print "<tr>";
						for ($i=0;$i<$cn-1;$i++){
							print "<td>".pg_result($r,$j,$i);
							$pd=pg_result($r,$j,4);
							if($pd<=date("Y-m-d"))
								$status='Paid';
							else if($pd>date("Y-m-d"))
								$status='In progress';
						}
						print "<td>".$status;
					}
					print "</table>";
				}
				
				if($userr){
					return $this->render('RentMovieBundle:Default:orders.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:orders.html.twig');
	}
	public function borrowedAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				
				$con=pg_connect("host=sbazy user=s175520 dbname=s175520 password=pfqrfbhf4");
				$q="select orderID,title,genre,paymentID,paymentDate from ((client right join orders using(clientID))left join movies using(movieID))right join payment using(paymentID) where login like '$username' and paymentDate<=now();";
				$r=pg_exec($con,$q);
				$rn=pg_numrows($r);
				$cn=pg_numfields($r);
				
				print "<h2 align=\"center\">List of borrowed movies:</h2><br/>";
				if ($rn>0)
				{
					print "<table class=\"table table-hover\">";

					print "<th>Order ID<th>Movie's title<th>Movie's genre<th>Payment ID";

					for ($j=0;$j<$rn;$j++)
					{
						print "<tr>";
						for ($i=0;$i<$cn-1;$i++){
							print "<td>".pg_result($r,$j,$i);
							$pd=pg_result($r,$j,4);
						}
						print "<form action=\"watch\">";
						print "<td><button type=\"submit\" class=\"btn btn-primary\">Watch</button>";
						print "</form>";
					}
					print "</table>";
				}
				
				if($userr){
					return $this->render('RentMovieBundle:Default:borrowed.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:borrowed.html.twig');
	}
	public function watchAction(){
		return $this->render('RentMovieBundle:Default:watch.html.twig');
	}
	public function reviewAction(Request $request){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				
				$a=$_SERVER['HTTP_REFERER'];
				$tokens = explode('/', $a);
				$mid = $tokens[sizeof($tokens)-1];
				$m=(int)$mid;
				
				$con=pg_connect("host=sbazy user=s175520 dbname=s175520 password=pfqrfbhf4");
				$q="Select clientID from client where login='$username'";
				$r=pg_exec($con,$q);
				$cid = pg_fetch_result($r, 0, 0);
				$c=(int)$cid;
				
				$rv=$request->get('moviereviews');
				
				$query = "INSERT INTO review(moviereviews, clientid, movieid) VALUES ('$rv', $c, $m);";
				$r=pg_exec($con,$query);
				echo "<script type='text/javascript'>alert('Review was successfully added!');</script>";
				switch($mid){
					case 1:
						$mo='remember';
						break;
					case 2:
						$mo='fifty';
						break;
					case 3:
						$mo='jessabelle';
						break;
					case 4:
						$mo='interstellar';
						break;
					case 5:
						$mo='club';
						break;
					case 6:
						$mo='frozen';
						break;
					case 7:
						$mo='boleyn';
						break;
					case 8:
						$mo='sniper';
						break;
					case 9:
						$mo='wars';
						break;
					case 10:
						$mo='words';
						break;
					case 11:
						$mo='legend';
						break;
					case 12:
						$mo='sniper';
						break;
				}
				
				if($userr){
					return $this->render("RentMovieBundle:Default:$mo.html.twig", array('name'=>$userr->getName()));
				}
			}
	}
	public function rememberAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:remember.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:remember.html.twig');
	}
	public function clubAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:club.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:club.html.twig');
	}
	public function warsAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:wars.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:wars.html.twig');
	}
	public function wordsAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:words.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:words.html.twig');
	}
	public function mindAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:mind.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:mind.html.twig');
	}
	public function fiftyAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:fifty.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:fifty.html.twig');
	}
	public function interstellarAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:interstellar.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:interstellar.html.twig');
	}
	public function jessabelleAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:jessabelle.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:jessabelle.html.twig');
	}
	public function sniperAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:sniper.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:sniper.html.twig');
	}
	public function legendAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:legend.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:legend.html.twig');
	}
	public function boleynAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:boleyn.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:boleyn.html.twig');
	}
	public function frozenAction(){
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:frozen.html.twig', array('name'=>$userr->getName()));
				}
			}
		return $this->render('RentMovieBundle:Default:frozen.html.twig');
	}
	public function romanceAction()
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:romance.html.twig', array('name'=>$userr->getName()));
				}
			}
        return $this->render('RentMovieBundle:Default:romance.html.twig', array());
    }
	 public function historyAction()
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:history.html.twig', array('name'=>$userr->getName()));
				}
			}
        return $this->render('RentMovieBundle:Default:history.html.twig', array());
    }
	 public function dramaAction()
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:drama.html.twig', array('name'=>$userr->getName()));
				}
			}
        return $this->render('RentMovieBundle:Default:drama.html.twig', array());
    }
	 public function horrorAction()
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:horror.html.twig', array('name'=>$userr->getName()));
				}
			}
        return $this->render('RentMovieBundle:Default:horror.html.twig', array());
    }
	 public function thrillerAction()
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:thriller.html.twig', array('name'=>$userr->getName()));
				}
			}
        return $this->render('RentMovieBundle:Default:thriller.html.twig', array());
    }
	 public function adventureAction()
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:adventure.html.twig', array('name'=>$userr->getName()));
				}
			}
        return $this->render('RentMovieBundle:Default:adventure.html.twig', array());
    }
	public function cartoonAction()
    {
		$session=$this->getRequest()->getSession();
		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('RentMovieBundle:Client');
		if($session->has('login')){
				$login = $session->get('login');
				$username=$login->getUsername();
				$password=$login->getPassword();
				$userr = $repository->findOneBy(array('login'=>$username,'password'=>$password));
				if($userr){
					return $this->render('RentMovieBundle:Default:cartoon.html.twig', array('name'=>$userr->getName()));
				}
			}
        return $this->render('RentMovieBundle:Default:cartoon.html.twig', array());
    }
}
