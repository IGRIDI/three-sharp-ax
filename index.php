<?php
$file_stream = fopen('in.txt', 'r');
if ($file_stream) {
    while (($buffer = fgets($file_stream)) !== false) {
    	$buffer = trim($buffer);
        $data_by_string[] = $buffer;
    }
}
fclose($file_stream);

//Берём массив строк ставок и "причёсываем" его к виду: $bets_by_game[GameID] = bet;
$count_of_bets = $data_by_string[0];
$array_of_bets_on_string = array_slice($data_by_string, 1, $count_of_bets);
$bets_by_game = [];
foreach ($array_of_bets_on_string as $bet){
    $bets_by_game[$bet[0]] = substr($bet, 2);
}
//Аналогично. $games_results[GameID] = result
$count_of_games = $data_by_string[(int)$count_of_bets+1];
$array_of_games_on_string = array_slice($data_by_string, $count_of_games+2);
$games_results = [];
foreach ($array_of_games_on_string as $game) {
	$games_results[$game[0]] = substr($game, 2);
}


//Смотрим результаты и накапливаем $bet_result
$bet_result= 0;
foreach ($games_results as $gameID => $result_of_game)
{
	//Разбиваем массив игры и ставки на соответствующие переменные
	$result_of_game = explode(' ', $result_of_game);
	$coefficient_of_loss = (float)$result_of_game[0];
	$coefficient_of_win = (float)$result_of_game[1];
	$coefficient_of_draw = 	(float)$result_of_game[2];
	$game_outcome = $result_of_game[3];

	if (!array_key_exists($gameID, $bets_by_game)) continue; // пропускаем игры, на которые не ставили
	$bet_on_game = explode(' ', $bets_by_game[$gameID]); // Разбиваем ставку
	$bet_rate = (int)$bet_on_game[0]; 		// размер ставки
	$bet_outcome = $bet_on_game[1]; 			// выбор команды


	if ($bet_outcome != $game_outcome){ //если проиграл
		$bet_result-= $bet_rate;	
	} else {				 									  //если выиграл
		switch ($bet_outcome) {
			case 'L': //Считаем выигрыш и убираем поставленные деньги
				$bet_result = $bet_result + $bet_rate * $coefficient_of_loss - $bet_rate;
				break;
			case 'R':
				$bet_result = $bet_result + $bet_rate * $coefficient_of_win - $bet_rate;
				break;
			case 'D':
				$bet_result = $bet_result + $bet_rate * $coefficient_of_draw - $bet_rate;
				break;
		}
	}
}

var_dump($bet_result);
$file_stream = fopen('out.txt', 'w+');
fwrite($file_stream, $bet_result);
fclose($file_stream);
