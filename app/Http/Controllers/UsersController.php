<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller{

    protected $model = User::class;
    protected $users, $user;

    public function index(Request $request) {
        $accHeader = $request->headers->get('Accept');
        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Acceptable', 404);
        }
        $this->users = $this->model::orderBy("id", "DESC")->paginate(2)->toArray();
        if($accHeader == 'application/json') {
            $response = [
                'total_count' => $this->users['total'],
                'limit' =>  $this->users['per_page'],
                'pagination' => [
                    'next_page' => $this->users['next_page_url'],
                    'prev_page' => $this->users['prev_page_url'],
                    'current_page' => $this->users['current_page'],
                ],
                'data' => $this->users['data']
            ];
            return response()->json($response, 200);
        }
        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<Users/>');
            foreach($this->users['data'] as $item) {
                $xmlItem = $xml->addChild('User');
                foreach ($item as $key => $value) {
                    $xmlItem->addChild($key, $value);
                }
            }
            return $xml->asXML();
        }
    }
    

    public function show(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        $this->user = $this->model::find($id);
        if(!$this->user) {abort(404);}

        if($accHeader == 'application/json') {
            return response()->json($this->user, 200);
        }

        if($accHeader == 'application/xml') {
            $xml = new \SimpleXMLElement('<Users/>');
            foreach ($this->user->getAttributes() as $key => $value) {
                $xml->addChild($key, $value);
            }
            return $xml->asXML();
        }
    }

    public function register(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'address'=> 'required|string',
            'phone' => 'required|numeric|digits_between:10,13',
            'gender' => 'required|in:pria,wanita',
        ]);
    
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $validator->errors()], 400);
            } else {
                $xml = new \SimpleXMLElement('<error/>');
                foreach ($validator->errors()->all() as $message) {
                    $xml->addChild('message', $message);
                }
                return $xml->asXML();
            }
        }
    
        $this->user = new User;
        $this->user->password = app('hash')->make($data['password']);
        $this->user->fill($data)->save();
    
        if ($request->wantsJson()) {
            return response()->json($this->user, 200);
        } else {
            $xml = new \SimpleXMLElement('<user/>');
            foreach ($this->user->toArray() as $key => $value) {
                $xml->addChild($key, $value);
            }
            return $xml->asXML();
        }
    }    

    public function update(Request $request, $id) {
        $accHeader = $request->headers->get('Accept');
        $contentTypeHeader = $request->headers->get('Content-Type');

        if($accHeader === '*/*' || empty($accHeader) ||
            ($accHeader != 'application/json' && $accHeader != 'application/xml'
            && $contentTypeHeader!= 'application/json' && $contentTypeHeader!= 'application/xml')) {
            return response('Not Accepttable', 404);
        }

        $this->user = $this->model::find($id);
        if(!$this->user) {abort(404);}

        if($accHeader == 'application/json' && $contentTypeHeader == 'application/json') {
            $data = $request->all();
            $validator = Validator::make($data, [
                'email' => 'required|email|exists:users',
                'password' => 'required',
                'address'=> 'required|string',
                'phone' => 'required|numeric|digits_between:10,13',
            ]);
            if ($validator->fails()) return response()->json(['error' => $validator->errors()], 400);
            $this->user->password = app('hash')->make($data['password']);
            $this->user->fill($data)->save();
            return response()->json($this->user, 200);
        }
        if($accHeader == 'application/xml' && $contentTypeHeader == 'application/xml') {
            $xmlString = $request->getContent();
            $xml = simplexml_load_string($xmlString);
            $data = json_decode(json_encode($xml), true);

            $validator = Validator::make($data, [
                'email' => 'required|email|exists:users',
                'password' => 'required',
                'address'=> 'required|string',
                'phone' => 'required|numeric|digits_between:10,13',
            ]);
            if ($validator->fails()) return response()->json(['error' => $validator->errors()], 400);

            $this->user->password = app('hash')->make($data['password']);
            $this->user->fill($data)->save();

            $xml = new \SimpleXMLElement('<Users/>');
            foreach ($this->user->getAttributes() as $key => $value) {
                $xml->addChild($key, $value);
            }
            return $xml->asXML();
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $validator->errors()], 400);
            } else {
                return response('Validation failed', 400);
            }
        }

        $credentials = $request->only(['email', 'password']);
        if (! $token = Auth::attempt($credentials)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            } else {
                return response('Unauthorized', 401);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ], 200);
        } else {
            $xml = new \SimpleXMLElement('<response/>');
            $xml->addChild('token', $token);
            $xml->addChild('token_type', 'bearer');
            $xml->addChild('expires_in', Auth::factory()->getTTL() * 60);
            return $xml->asXML();
        }
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
    
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Successfully logged out'], 200);
        } else {
            $xml = new \SimpleXMLElement('<response/>');
            $xml->addChild('message', 'Successfully logged out');
            return $xml->asXML();
        }
    }
    
    public function delete($id)
    {
        try {
            $user = User::find($id);
    
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
    
            $user->delete();
    
            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user', 'error' => $e->getMessage()], 500);
        }
    }
}
