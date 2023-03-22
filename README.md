# ZADANIE  

### Hipo - Hipotetyczny Kalkulator Kredytu Hipotecznego
Aplikacja posiada pierwszą wersję funkcjonalności, jaką jest obliczenie wyników kredytu hipotecznego dla podanych parametrów.

Użytkownik, wywołując endpoint GET `/api/mortgage` z parametrami:

* creditValue - wartość kredytu, (wartość liczbowa w PLN, pole wymagane)
* secureValue - wartość zabezpieczenia, (wartość liczbowa w PLN, pole wymagane)
* age - wiek kredytobiorcy (wartość liczbowa w latach, pole wymagane)
* provision - prowizja dla banku za udzielenie kredytu, (wartość liczbowa w %, pole wymagane)
* margin - oprocentowanie, (wartość liczbowa w %, pole wymagane)
* period - okres kredytowania w miesiącach, (wartość liczbowa, pole wymagane)
* email - pole opcjonalne, na który wysyłany jest email z obliczeniami


otrzymuje w odpowiedzi:

* initialCostValue - koszt początkowy (wartość liczbowa w PLN)
* installmentValue - wysokość raty stałej (wartość liczbowa w PLN)
* totalValue - cała kwota zapłacona bankowi za kredyt (wartość liczbowa w PLN)
* totalCostValue - całkowity koszt kredytu (wartość liczbowa w PLN)


Jeśli wywołanie endpointu zawiera parametr email, na podany adres wysłane jest podsumowanie zapytania z obliczonymi wynikami kredytu

### Hipo 2.0 - Hipotetyczny Kalkulator Kredytu Hipotecznego wersja 2
W związku z rozwojem aplikacji, planowane jest wydanie wersji rozszerzonej, która posiada następujące wymagania biznesowo-techniczne:

1. Parametry endpointu GET `/api/mortgage` powinny posiadać pewne ograniczenia:

* minimalna kwota kredytu: 100000
* maksymalna kwota kredytu 10000000
* maksymalny poziom LTV: 90
* minimalny okres kredytowania: 12 miesięcy
* maksymalny okres kredytowania: 40 lat
* maksymalny wiek kredytobiorcy w momencie spłaty ostatniej raty nie powinien przekraczać 70 lat

W odpowiedzi powinny być zwracane właściwe komunikaty błędów, statusu odpowiedzi 422 Unprocessable Content.
Endpoint nie powinien zawierać wsparcia dla funkcji wysyłania emaila

2. Wydzielenie funkcjonalności odpowiedzialnej za wysłanie emaila z podsumowaniem obliczeń poprzez utworzenie nowego endpointu 

* wywołanie POST `/api/mortgage/email` z parametrami takimi samymi jak dla endpointu GET `/api/mortgage`
* powinny być spełnione takie same warunki walidacji przesłanych parametrów 
* dla pomyśnego wyniku, status odpowiedzi 202 Accepted
* ze względów wydajności dobrze, by operacja wysłania emaila była asynchroniczna

3. Rozbudowanie szablonu wysyłanego emaila o harmonogram spłaty rat stałych

### Użyte technologie i narzędzia
* PHP.8.2
* Symfony 6.2
* API Platform 3.0
* Messenger i RabbitMQ
* PHPUnit
* PhpStan
* CS Fixer
* Mailhog
* brick/math i math-php

### Uruchomienie Aplikacji
* uruchom projekt w dokerze (docker-compose up --build -d), jeśli potrzeba zainstaluj wszystkie potrzebne zależności wewnąrz kontenera php (docker-compose exec web bash)
* po uruchomieniu projekt jest dostępny pod adresem: `http://localhost:8090/api`
* projekt w dokerze zawiera developerski serwer smtp, UI pod adresem: `http://localhost:8025`
* w repozytorium umieszczona jest kolekcja postmana z requestem do endpointu API

### Rozwiązanie zadania
* powinno zawierać rozbudowaną (wg wymagań v2) i działającą aplikację
* sklonuj repozytorium projektu i umieść we własnym git
* utwórz branch na zadanie i tam koduj
* utwórz PR i dołącz mnie jako recenzenta
* termin na wysłanie rozwiązania - 7 dni od otrzymania zadania

### Na co zwrócić uwagę
* kod powinien spełniać standardy PSR-12
* być otypowany i przechodzić testy analizy statycznej na 6 poziomie PHPStan
* rozwiązanie zadania wymaga przeprowadzenia pewnego refaktoringu - pamietaj, by kod spełniał założenia Clean Code, był SOLID oraz DRY.
* staraj się wykorzystać utworzone interfejsy i dto, nie krępuj się ich zmieniać i rozbudowywać.
* testy, nawet jeśli nie dają pełnego pokrycia są lepsze niż ich brak :)
