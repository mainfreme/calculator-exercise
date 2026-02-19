Epic: Rozbudowa kalkulatora hipotecznego i asynchroniczna wysyłka email
Cel: Wdrożenie ścisłej walidacji parametrów kredytu, wydzielenie wysyłki email do osobnego, asynchronicznego endpointu oraz rozbudowa raportu o harmonogram spłaty.

Zadanie 1: Utworzenie współdzielonego walidatora parametrów kredytu hipotecznego
Typ: Task / Backend
Opis:
Zarówno istniejący endpoint GET, jak i nowy endpoint POST będą wymagały identycznej walidacji danych wejściowych. Należy utworzyć wydzieloną klasę/serwis walidujący, aby uniknąć duplikacji kodu (zasada DRY).

Kryteria akceptacji (Acceptance Criteria):

[ ] Walidator sprawdza czy kwota kredytu wynosi minimum 100 000 i maksimum 10 000 000.

[ ] Walidator sprawdza czy wskaźnik LTV (Loan-to-Value) wynosi maksymalnie 90.

[ ] Walidator sprawdza czy okres kredytowania wynosi minimum 12 miesięcy i maksimum 480 miesięcy (40 lat).

[ ] Walidator wylicza wiek kredytobiorcy na koniec okresu kredytowania (wiek w momencie wnioskowania + okres kredytowania) i sprawdza, czy nie przekracza on 70 lat.

[ ] W przypadku błędu walidacji, system zwraca odpowiednie, czytelne komunikaty błędów dla każdego naruszonego warunku.

[ ] Napisano testy jednostkowe pokrywające wszystkie przypadki brzegowe walidacji.

Zadanie 2: Refaktoryzacja endpointu GET /api/mortgage
Typ: Task / Backend
Opis:
Należy zaktualizować obecny endpoint służący do kalkulacji hipoteki, wdrażając nowe zasady walidacji i usuwając z niego logikę wysyłki powiadomień.

Kryteria akceptacji (Acceptance Criteria):

[ ] Endpoint wykorzystuje wspólny walidator z Zadania 1.

[ ] W przypadku niezgodności danych wejściowych z regułami, endpoint zwraca kod HTTP 422 Unprocessable Content wraz z listą błędów.

[ ] Usunięto z endpointu logikę odpowiedzialną za wysyłanie emaila z podsumowaniem (endpoint ma służyć wyłącznie do kalkulacji).

[ ] Zaktualizowano testy integracyjne dla endpointu GET, w tym testy weryfikujące poprawność zwracanego kodu 422.

Zadanie 3: Implementacja asynchronicznego mechanizmu wysyłki emaili
Typ: Task / Infrastructure / Backend
Opis:
Ze względów wydajnościowych wysyłka maili nie może blokować wątku żądania HTTP. Należy przygotować infrastrukturę/logikę pozwalającą na kolejkowanie zadań wysyłki. (Uwaga dla zespołu: dobór technologii zależy od stacku projektu - np. RabbitMQ, Redis/Sidekiq, Spring @Async).

Kryteria akceptacji (Acceptance Criteria):

[ ] Przygotowano mechanizm (kolejkę/job/workera) odbierający zlecenia wysłania maila.

[ ] Błędy podczas wysyłki maila (np. timeout SMTP) są logowane, a system posiada mechanizm ponowienia próby (retry) w przypadku miękkich błędów.

Zadanie 4: Utworzenie nowego endpointu POST /api/mortgage/email
Typ: Task / Backend
Opis:
Utworzenie dedykowanego endpointu, który przyjmie parametry kalkulacji i zleci w tle wysyłkę podsumowania do klienta.

Kryteria akceptacji (Acceptance Criteria):

[ ] Utworzono endpoint POST /api/mortgage/email przyjmujący dokładnie to samo body/parametry co endpoint GET /api/mortgage.

[ ] Endpoint wykorzystuje ten sam walidator danych wejściowych (z Zadania 1).

[ ] W przypadku błędu walidacji zwracany jest kod 422 Unprocessable Content z odpowiednimi komunikatami.

[ ] W przypadku poprawnej walidacji, endpoint przekazuje zadanie wysyłki maila do mechanizmu asynchronicznego (z Zadania 3).

[ ] Natychmiast po udanej walidacji i zakolejkowaniu wiadomości, endpoint zwraca status 202 Accepted bez czekania na faktyczne wysłanie maila.

[ ] Utworzono dokumentację Swagger/OpenAPI dla nowego endpointu.

Zadanie 5: Generowanie harmonogramu rat stałych i rozbudowa szablonu email
Typ: Task / Backend & Frontend (Email Template)
Opis:
Email wysyłany do klienta musi zostać wzbogacony o szczegółowy harmonogram spłaty kredytu w wariancie rat stałych.

Kryteria akceptacji (Acceptance Criteria):

[ ] Opracowano logikę wyliczającą harmonogram spłaty (kolejne numery rat, kapitał, odsetki, saldo po spłacie) dla rat stałych.

[ ] Zaktualizowano szablon HTML (oraz ewentualnie Plain Text) wiadomości email.

[ ] Harmonogram jest prezentowany w czytelnej formie (np. tabela z podziałem na miesiące/lata).

[ ] [Opcjonalnie - jeśli ma to zastosowanie] W przypadku bardzo długich harmonogramów (np. 40 lat = 480 rat), rozważyć generowanie harmonogramu w formie załącznika PDF, a w treści maila umieszczenie tylko rocznego podsumowania (zależy od ustaleń biznesowych).

[ ] Sprawdzono renderowanie szablonu na popularnych klientach pocztowych.
