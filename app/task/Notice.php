<?php
/**
 * 通知任务类
 */

namespace app\task;
class Notice
{
    /**
     * 通知所有在线的客户端
     * @param $fd       发起请求的FD
     * @param $data     要发送的内容
     *
     * @return bool
     */
    public function ToAll($fd, $data)
    {
        $fds = [];
        foreach ($this->server->connections as $client_fd) {
            if ($fd != $client_fd && $this->server->exist($client_fd)) {
                $this->server->push($client_fd, $data);
                $fds[] = $client_fd;
            }
        }
        return "已向[" . join(",", $fds) . "]发送通知内容：" . $data;
    }
}