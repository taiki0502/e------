<?php

// 引数が十分に渡されているかを確認
if ($argc < 2) {
    echo "使い方: php FileManipulator.php [コマンド] [オプション...]\n";
    exit(1);
}

$command = $argv[1];

// ファイルの存在確認関数
function check_file_exists($filepath) {
    if (!file_exists($filepath)) {
        echo "ファイルが存在しません: $filepath\n";
        exit(1);
    }
}

// マルチバイト文字列を逆順にする関数
function mb_strrev($str, $encoding = 'UTF-8') {
    $length = mb_strlen($str, $encoding);
    $reversed = '';
    while ($length-- > 0) {
        $reversed .= mb_substr($str, $length, 1, $encoding);
    }
    return $reversed;
}

// マルチバイト対応のstr_replace関数
function mb_str_replace($needle, $replacement, $haystack, $encoding = 'UTF-8') {
    $needle_len = mb_strlen($needle, $encoding);
    $replacement_len = mb_strlen($replacement, $encoding);
    
    $pos = mb_strpos($haystack, $needle, 0, $encoding);
    while ($pos !== false) {
        $haystack = mb_substr($haystack, 0, $pos, $encoding) . $replacement . mb_substr($haystack, $pos + $needle_len, null, $encoding);
        $pos = mb_strpos($haystack, $needle, $pos + $replacement_len, $encoding);
    }
    return $haystack;
}

// ファイルの内容を逆順にする関数
function reverse_file($input_path, $output_path) {
    if (!file_exists($input_path)) {
        echo "ファイルが存在しません: $input_path\n";
        exit(1);
    }

    // ファイルの内容をUTF-8として読み込む
    $content = file_get_contents($input_path);
    
    // 文字列を逆順にする（マルチバイト対応）
    $reversed_content = mb_strrev($content, 'UTF-8');
    
    // 逆順にした内容を出力ファイルに書き込む
    file_put_contents($output_path, $reversed_content);
    echo "ファイル内容を逆順にしました: $output_path\n";
}

// ファイルをコピーする関数
function copy_file($input_path, $output_path) {
    check_file_exists($input_path);
    
    if (copy($input_path, $output_path)) {
        echo "ファイルをコピーしました: $output_path\n";
    } else {
        echo "ファイルのコピーに失敗しました\n";
    }
}

// ファイルの内容をn回複製する関数
function duplicate_contents($input_path, $n) {
    check_file_exists($input_path);
    
    // ファイルの内容を読み込む
    $content = file_get_contents($input_path);
    
    // 内容をn回複製
    $duplicated_content = str_repeat($content, (int)$n);
    
    // 複製された内容を書き込む
    file_put_contents($input_path, $duplicated_content);
    echo "ファイル内容を{$n}回複製しました: $input_path\n";
}

// ファイル内の文字列を置き換える関数
function replace_string_in_file($input_path, $needle, $new_string) {
    check_file_exists($input_path);
    
    // ファイルの内容を読み込む
    $content = file_get_contents($input_path);
    
    // 文字列を置き換える（マルチバイト対応）
    $updated_content = mb_str_replace($needle, $new_string, $content, 'UTF-8');
    
    // 更新された内容を書き込む
    file_put_contents($input_path, $updated_content);
    echo "'$needle'を'$new_string'に置き換えました: $input_path\n";
}

// コマンドに応じて処理を実行
switch ($command) {
    case 'reverse':
        if ($argc < 4) {
            echo "使い方: php FileManipulator.php reverse [入力ファイルパス] [出力ファイルパス]\n";
            exit(1);
        }
        reverse_file($argv[2], $argv[3]);
        break;
    
    case 'copy':
        if ($argc < 4) {
            echo "使い方: php FileManipulator.php copy [入力ファイルパス] [出力ファイルパス]\n";
            exit(1);
        }
        copy_file($argv[2], $argv[3]);
        break;
    
    case 'duplicate-contents':
        if ($argc < 4) {
            echo "使い方: php FileManipulator.php duplicate-contents [入力ファイルパス] [回数]\n";
            exit(1);
        }
        duplicate_contents($argv[2], $argv[3]);
        break;
    
    case 'replace-string':
        if ($argc < 5) {
            echo "使い方: php FileManipulator.php replace-string [入力ファイルパス] [置き換え対象文字列] [新しい文字列]\n";
            exit(1);
        }
        replace_string_in_file($argv[2], $argv[3], $argv[4]);
        break;
    
    default:
        echo "無効なコマンドです。利用可能なコマンドは: reverse, copy, duplicate-contents, replace-string\n";
        exit(1);
}

?>