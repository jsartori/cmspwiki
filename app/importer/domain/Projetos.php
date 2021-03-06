<?php
class app_importer_domain_Projetos {
	private static $instance;
	private function __construct(){
	}
	
	public static function getInstance(){
		if (!isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function handle(){
		$this->indexHandler();
	}
	
	public function indexHandler(){
		//$url = 'http://www2.camara.sp.gov.br/projetos/projetos.txt';
		$url = './dadosExt/projetos.txt';
		echo "import Projetos\n";
		echo "url: $url \n";
		$this->import($url);
		echo "\nfim\n";
	}
	
	public function import($url){
		$projetoAoDb = new app_importer_ao_db_Projetos();
		$projetoAoDb->truncate();
		$projetoBeanDb = new app_importer_bean_db_Projetos();
		$i = 0;
		$handle = fopen($url,'r');
		
		while (!feof($handle)) {
			if ($dataStr = fgets($handle)) {
				$data = explode('#', $dataStr);
				if ($i!= 0 && count($data) == 7){
					$projetoBeanDb->id = 0;
					$projetoBeanDb->tipo_projeto = strtoupper(utf8_encode($data[0]));
					$projetoBeanDb->numero_projeto = strtoupper(utf8_encode($data[1]));
					$projetoBeanDb->data_projeto = strtoupper((utf8_encode($data[2])));
					$projetoBeanDb->ementa = strtoupper((utf8_encode($data[3])));
					$projetoBeanDb->tipo_norma = strtoupper(utf8_encode($data[4]));
					$projetoBeanDb->numero_norma = strtoupper((utf8_encode($data[5])));
					$projetoBeanDb->data_norma = strtoupper((utf8_encode($data[6])));
					
					$projetoAoDb->upsert($projetoBeanDb);
				}
				$i++;
			}
		}
		
		/*
		// para fgetscv tem erro de formatação para o delimitador de texto padrão '"' 
		while (($data = fgetcsv($handle,0,'#',"\x01")) !== FALSE) {
			if($i!=0 && count($data) == 7){
				$projetoBeanDb->id = 0;
				$projetoBeanDb->tipo_projeto = strtoupper(utf8_encode($data[0]));
				$projetoBeanDb->numero_projeto = strtoupper(utf8_encode($data[1]));
				$projetoBeanDb->data_projeto = strtoupper((utf8_encode($data[2])));
				$projetoBeanDb->ementa = strtoupper((utf8_encode($data[3])));
				$projetoBeanDb->tipo_norma = strtoupper(utf8_encode($data[4]));
				$projetoBeanDb->numero_norma = strtoupper((utf8_encode($data[5])));
				$projetoBeanDb->data_norma = strtoupper((utf8_encode($data[6])));
				
				$projetoAoDb->upsert($projetoBeanDb);
			}
			$i++;
		}
		*/
		fclose ($handle);
	}
}
