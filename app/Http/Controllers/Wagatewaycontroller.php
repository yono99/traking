<?php

namespace App\Http\Controllers;

use App\Models\WaSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class WaGatewayController extends Controller
{
    private string $gateway;

    public function __construct()
    {
        $this->gateway = config('services.wa_gateway.url', 'https://wagateway.catatankuliah.my.id');
    }

    public function index()
    {
        $sessions = WaSession::orderByDesc('is_active')->orderByDesc('created_at')->get();
        return Inertia::render('WaGateway', ['sessions' => $sessions]);
    }

    public function sessions()
    {
        return response()->json(WaSession::orderByDesc('is_active')->orderByDesc('created_at')->get());
    }

    public function activeSession()
    {
        $session = WaSession::where('is_active', true)->first();
        if (!$session) {
            return response()->json(['session_name' => null, 'message' => 'Tidak ada session aktif'], 404);
        }
        return response()->json(['session_name' => $session->session_name]);
    }

    public function start()
    {
        $sessionName = 'wa-' . strtolower(substr(md5(uniqid()), 0, 8));
        $session = WaSession::create([
            'session_name' => $sessionName,
            'is_active'    => false,
        ]);
        return response()->json($session, 201);
    }

public function qr(string $sessionName)
{
    try {
        $res = Http::withoutVerifying()->timeout(60)->get("{$this->gateway}/session/start", [
            'session' => $sessionName,
        ]);

        if ($res->failed()) {
            return response()->json(['message' => 'Gagal ambil QR'], 502);
        }

        preg_match("/let qr = '(data:image\/png;base64,[^']+)'/", $res->body(), $matches);

        if (empty($matches[1])) {
            return response()->json(['message' => 'QR tidak ditemukan'], 404);
        }

        return response()->json(['qr' => $matches[1]]);

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 504);
    }
}

  public function status(string $sessionName)
{
    try {
        $res = Http::withoutVerifying()->timeout(10)->get("{$this->gateway}/session");
        $data = $res->json();
        $sessions = $data['data'] ?? [];
        
        $status = in_array($sessionName, $sessions) ? 'connected' : 'disconnected';

        if ($status === 'connected') {
            WaSession::where('is_active', true)->update(['is_active' => false]);
            WaSession::where('session_name', $sessionName)->update(['is_active' => true]);
        }

        return response()->json(['status' => $status]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

    public function destroy(int $id)
    {
        $session = WaSession::findOrFail($id);
        try {
            Http::withoutVerifying()->timeout(10)->get("{$this->gateway}/session/logout", [
                'session' => $session->session_name,
            ]);
        } catch (\Exception $e) {}

        $session->delete();
        return response()->json(['message' => 'Session dihapus']);
    }

    public function sendMessage(Request $request)
    {
        $session = WaSession::where('is_active', true)->first();
        if (!$session) {
            return response()->json(['message' => 'Tidak ada session WA aktif'], 404);
        }

        try {
            $res = Http::withoutVerifying()->timeout(15)->post("{$this->gateway}/message/send-text", [
                'session' => $session->session_name,
                'to'      => $request->to,
                'text'    => $request->text,
            ]);

            if ($res->failed()) {
                return response()->json(['message' => 'Gateway gagal: ' . $res->body()], 502);
            }

            return response()->json(['message' => 'Terkirim']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 504);
        }
    }
    public function sendImage(Request $request)
{
    $session = WaSession::where('is_active', true)->first();
    if (!$session) {
        return response()->json(['message' => 'Tidak ada session WA aktif'], 404);
    }

    try {
        $res = Http::withoutVerifying()->timeout(15)->post("{$this->gateway}/message/send-image", [
            'session'   => $session->session_name,
            'to'        => $request->to,
            'text'      => $request->text,
            'image_url' => $request->image_url,
        ]);

        if ($res->failed()) {
            return response()->json(['message' => 'Gateway gagal: ' . $res->body()], 502);
        }

        return response()->json(['message' => 'Terkirim']);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 504);
    }
}
}