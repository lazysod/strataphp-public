<?php 
class bserror
{

    public function success($message)
    {
        $content = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;
    }

    public function danger($message)
    {
        $content = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;        
    }

    public function warning($message)
    {
        $content = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;        
    }

    public function primary($message)
    {
        $content = '<div class="alert alert-primary alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;        
    }

    public function secondary($message)
    {
        $content = '<div class="alert alert-secondary alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;        
    }

    public function info($message)
    {
        $content = '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;        
    }

    public function light($message)
    {
        $content = '<div class="alert alert-light alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;        
    }

    public function dark($message)
    {
        $content = '<div class="alert alert-dark alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        return $content;        
    }
}

?>