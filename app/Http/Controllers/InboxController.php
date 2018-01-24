<?php

namespace App\Http\Controllers;
use App\Conversation;
use Evilnet\Inbox\Controller;
use Evilnet\Inbox\Services\InboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class InboxController extends Controller
{

    protected $inboxService;

    public function __construct(InboxService $inboxService)
    {
        $this->inboxService = $inboxService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $conversations = $this->inboxService->fetchAllConversation();

        $users = $this->inboxService->getInboxUsers($conversations);


        return view('inbox.index', compact('conversations', 'users'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inbox.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                'message' => 'required|max:10000',
                'subject' => 'required',
            ]
        );

        $message = $this->encrypt($request->get('message'), env('APP_KEY'));
        $this->inboxService->addConversation('admin', $message, $request->get('subject'));
    }

    /**
     * Display the specified resource.
     *
     * @param conversation|int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function show($id)
    {
        $conversation = Conversation::find($id);
        foreach ($conversation->messages as $message){
            $message->message = $this->decrypt($message->message, env('APP_KEY'));
        }
        if(auth()->id() == $conversation->id_to OR auth()->id() == $conversation->id_from) {
            return view('inbox.show')->with('conversation', $conversation);
        }
        return redirect()->back();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param conversation|int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function addMessage(Request $request, Conversation $id)
    {
        $this->validate($request,
            [
                'message' => 'required|max:10000'
            ]
        );
        if(auth()->id() == $id->id_to OR auth()->id() == $id->id_from) {
            $message = $this->encrypt($request->get('message'), env('APP_KEY'));
            $request->message = $message;
            $request->merge(['message' => $message]);
            $this->inboxService->addMessage($request, $id);
        }
        return redirect()->back();

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $id)
    {
        if(auth()->id() == $id->id_to OR auth()->id() == $id->id_from) {

            $this->inboxService->deleteConversation($id);
        }
        return redirect()->back();

    }

    private function encrypt($data, $secret)
    {

        //Generate a key from a hash
        //dd(openssl_get_cipher_methods());
            $key = md5(utf8_encode($secret), true);

        //Take first 8 bytes of $key and append them to the end of $key.
        $key .= substr($key, 0, 8);

        //Pad for PKCS7
        $blockSize = 64;
        $len = strlen($data);
        $pad = $blockSize - ($len % $blockSize);
        $data .= str_repeat(chr($pad), $pad);
        //Encrypt data
        $encData = openssl_encrypt($data, 'des-ecb', $key);

        return base64_encode($encData);
    }

    private function decrypt($data, $secret)
    {
        //Generate a key from a hash
        $key = md5(utf8_encode($secret), true);

        //Take first 8 bytes of $key and append them to the end of $key.
        $key .= substr($key, 0, 8);

        $data = base64_decode($data);

        $data = openssl_decrypt($data, 'des-ecb', $key);

        $len = strlen($data);
        $pad = ord($data[$len-1]);

        return substr($data, 0, strlen($data) - $pad);
    }
}
