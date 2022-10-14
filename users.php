<?
// Brak komentarzy, które mogłyby naprowadzić inego deva
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class usersTasks    //można by dodać klase
{
    public function updateUsers($users)
    {   // Osobiście nie przenosze braketów do kolejnych linijek
        foreach ($users as $user) {
            try {
                if ($user['name'] && $user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10) //brakuje sprawdzeń np. if(!empty($user['name'])...)
                    DB::table('users')->where('id', $user['id'])->update([
                        'name' => $user['name'],
                        'login' => $user['login'],
                        'email' => $user['email'],
                        'password' => md5($user['password']) // Zmiana md5 na lepsze zabezpieczenie np. password_hash()
                    ]);
            } catch (\Throwable $e) {
                return Redirect::back()->withErrors(['error', ['We couldn\'t update user: ' . $e->getMessage()]]);
            }
        }
        return Redirect::back()->with(['success', 'All users updated.']);
    }

    public function storeUsers($users)
    {   // Osobiście nie przenosze braketów do kolejnych linijek
        // Redukcja pustch przestrzeni w kodzie
        foreach ($users as $user) {
            try {
                if ($user['name'] && $user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10) //brakuje sprawdzeń np. if(!empty($user['name'])...)
                    DB::table('users')->insert([
                        'name' => $user['name'],
                        'login' => $user['login'],
                        'email' => $user['email'],
                        'password' => md5($user['password']) // Zmiana md5 na lepsze zabezpieczenie np. password_hash()
                    ]);
            } catch (\Throwable $e) {
                return Redirect::back()->withErrors(['error', ['We couldn\'t store user: ' . $e->getMessage()]]);
            }
        }
        $this->sendEmail($users);
        return Redirect::back()->with(['success', 'All users created.']);
    }

    private function sendEmail($users)
    {   // Osobiście nie przenosze braketów do kolejnych linijek
        foreach ($users as $user) {
            $message = 'Account has beed created. You can log in as <b>' . $user['login'] . '</b>';
            if ($user['email']) { // if(!empty($user['emial']))... w momencie kiedy np. w funkcji storeUsers() dane do $user['email'] są bez uzupełnionego adresu @ 
                Mail::to($user['email'])
                    ->cc('support@company.com')
                    ->subject('New account created')
                    ->queue($message);
            }
        }
        return true;
    }
}
