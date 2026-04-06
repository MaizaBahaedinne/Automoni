<?php

namespace App\Controllers;

use App\Models\PostModel;
use App\Models\PostReactionModel;
use App\Models\PostCommentModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class PostController extends BaseController
{
    private int $userId;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->userId = (int) (session()->get('user_id') ?? 0);
    }

    // ── Create ─────────────────────────────────────────────────────────────

    public function store(): RedirectResponse
    {
        $type    = $this->request->getPost('type') ?? 'text';
        $content = trim($this->request->getPost('content') ?? '');

        $allowedTypes = ['text', 'image', 'video', 'announcement'];
        if (!in_array($type, $allowedTypes, true)) {
            return redirect()->back()->with('error', 'Type de publication invalide.');
        }

        if ($type === 'text' && $content === '') {
            return redirect()->back()->with('error', 'Le contenu ne peut pas être vide.');
        }
        if ($type === 'announcement' && $content === '') {
            return redirect()->back()->with('error', 'Décrivez votre annonce.');
        }

        $data = [
            'user_id' => $this->userId,
            'type'    => $type,
            'content' => $content ?: null,
        ];

        // Announcement subtype
        if ($type === 'announcement') {
            $sub = $this->request->getPost('announcement_subtype') ?? 'other';
            $allowedSubs = ['new_job', 'open_to_work', 'certification', 'promotion', 'other'];
            $data['announcement_subtype'] = in_array($sub, $allowedSubs, true) ? $sub : 'other';
        }

        // Video URL
        if ($type === 'video') {
            $videoUrl = trim($this->request->getPost('video_url') ?? '');
            if ($videoUrl !== '') {
                $data['video_url'] = filter_var($videoUrl, FILTER_SANITIZE_URL);
            }
        }

        // File upload (image or video file)
        if (in_array($type, ['image', 'video'], true)) {
            $file = $this->request->getFile('media_file');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $allowedMime = $type === 'image'
                    ? ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
                    : ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
                $maxBytes = $type === 'image' ? 10 * 1024 * 1024 : 200 * 1024 * 1024;

                if (!in_array($file->getMimeType(), $allowedMime, true)) {
                    return redirect()->back()->with('error', 'Format de fichier non supporté.');
                }
                if ($file->getSize() > $maxBytes) {
                    $maxLabel = $type === 'image' ? '10 Mo' : '200 Mo';
                    return redirect()->back()->with('error', "Fichier trop volumineux (max {$maxLabel}).");
                }

                $dest = WRITEPATH . 'uploads/posts/';
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
                $fname = 'post_' . $this->userId . '_' . time() . '.' . $file->getClientExtension();
                $file->move($dest, $fname);
                $data['media_file'] = $fname;
            } elseif ($type === 'image' && !isset($data['media_file'])) {
                return redirect()->back()->with('error', 'Veuillez sélectionner une image.');
            }
        }

        model(PostModel::class)->insert($data);
        return redirect()->to('/')->with('success', 'Publication créée.');
    }

    // ── Delete ─────────────────────────────────────────────────────────────

    public function destroy(int $id): RedirectResponse
    {
        $post = model(PostModel::class)->find($id);
        if (!$post || (int) $post->user_id !== $this->userId) {
            return redirect()->to('/')->with('error', 'Publication introuvable.');
        }
        if (!empty($post->media_file)) {
            @unlink(WRITEPATH . 'uploads/posts/' . $post->media_file);
        }
        model(PostModel::class)->delete($id);
        // Cascade reactions and comments
        model(PostReactionModel::class)->where('post_id', $id)->delete();
        model(PostCommentModel::class)->where('post_id', $id)->delete();
        return redirect()->to('/')->with('success', 'Publication supprimée.');
    }

    // ── React (AJAX) ───────────────────────────────────────────────────────

    public function react(int $id): ResponseInterface
    {
        $post = model(PostModel::class)->find($id);
        if (!$post) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }
        $reactionType = $this->request->getPost('type') ?? 'like';
        $added   = model(PostReactionModel::class)->toggle($id, $this->userId, $reactionType);
        $updated = model(PostModel::class)->find($id);
        return $this->response->setJSON([
            'reacted' => $added,
            'count'   => (int) $updated->reactions_count,
        ]);
    }

    // ── Load comments (AJAX) ───────────────────────────────────────────────

    public function comments(int $id): ResponseInterface
    {
        $items = model(PostCommentModel::class)->getForPost($id);
        return $this->response->setJSON([
            'html'  => $this->renderComments($items),
            'count' => count($items),
        ]);
    }

    // ── Add comment (AJAX) ─────────────────────────────────────────────────

    public function addComment(int $id): ResponseInterface
    {
        $content = trim($this->request->getPost('content') ?? '');
        if ($content === '') {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Commentaire vide.']);
        }
        $post = model(PostModel::class)->find($id);
        if (!$post) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }
        model(PostCommentModel::class)->addComment($id, $this->userId, $content);
        $items = model(PostCommentModel::class)->getForPost($id);
        return $this->response->setJSON([
            'html'  => $this->renderComments($items),
            'count' => count($items),
        ]);
    }

    // ── Helper: render comments to HTML ───────────────────────────────────

    private function renderComments(array $items): string
    {
        if (empty($items)) {
            return '<p style="color:#aaa;font-size:13px;padding:8px 0;">Aucun commentaire. Soyez le premier !</p>';
        }
        $html = '';
        foreach ($items as $c) {
            $name = esc($c->first_name . ' ' . $c->last_name);
            $av   = !empty($c->avatar) ? base_url('uploads/' . esc($c->avatar)) : null;
            try {
                $diff = (new DateTime($c->created_at))->diff(new DateTime());
                $ago  = $diff->days > 0
                    ? $diff->days . 'j'
                    : ($diff->h > 0 ? $diff->h . 'h' : max(1, $diff->i) . 'min');
            } catch (\Exception $e) {
                $ago = '';
            }

            $avatarHtml = $av
                ? '<img src="' . $av . '" class="post-cmt-av" alt="">'
                : '<div class="post-cmt-av post-cmt-av-init">' . strtoupper(substr($c->first_name ?? 'U', 0, 1)) . '</div>';

            $html .= '<div class="post-cmt-row">'
                . $avatarHtml
                . '<div class="post-cmt-bubble">'
                . '<strong>' . $name . '</strong>'
                . ($ago ? ' <span class="post-cmt-time">' . $ago . '</span>' : '')
                . '<div class="post-cmt-text">' . nl2br(esc($c->content)) . '</div>'
                . '</div>'
                . '</div>';
        }
        return $html;
    }
}
