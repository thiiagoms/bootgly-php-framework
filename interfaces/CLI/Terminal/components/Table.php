<?php
/*
 * --------------------------------------------------------------------------
 * Bootgly PHP Framework
 * Developed by Rodrigo Vieira (@rodrigoslayertech)
 * Copyright 2020-present
 * Licensed under MIT
 * --------------------------------------------------------------------------
 */

namespace Bootgly\CLI\Terminal\components;


use Bootgly\__String;
use Bootgly\CLI\Terminal\Output;


class Table
{
   private Output $Output;

   // * Config
   // * Data
   private $headers;
   private $footers;
   private $rows;
   // @ Style
   private array $borders;
   // * Meta


   public function __construct (Output $Output)
   {
      $this->Output = $Output;

      // * Config
      // * Data
      $this->headers = [];
      $this->footers = [];
      $this->rows = [];
      // @ Style
      $this->borders = [
         'top'          => '═',
         'top-left'     => '╔',
         'top-mid'      => '╤',
         'top-right'    => '╗',

         'bottom'       => '═',
         'bottom-left'  => '╚',
         'bottom-mid'   => '╧',
         'bottom-right' => '╝',

         'mid'          => '─',
         'mid-left'     => '╟',
         'mid-mid'      => '┼',
         'mid-right'    => '╢',
         'middle'       => '│ ',

         'left'         => '║',
         'right'        => '║',
      ];
   }

   // ! Header(s)
   public function setHeaders (array $headers)
   {
      $this->headers = $headers;
   }
   // ! Footer(s)
   public function setFooters (array $footers)
   {
      $this->footers = $footers;
   }

   // ! Row(s)
   public function addRow (array $row)
   {
      $this->rows[] = $row;
   }
   public function setRows (array $rows)
   {
      $this->rows = $rows;
   }

   private function printRow (array $row, array $columnWidths)
   {
      $output = $this->borders['left'] . ' ';

      foreach ($row as $key => $value) {
         if ($key > 0) {
            $output .= ' ' . $this->borders['middle'];
         }

         $output .= __String::pad($value, $columnWidths[$key], ' ', STR_PAD_RIGHT);

         if ($key > 0) {
            $output .= ' ';
         }
      }

      $output .= $this->borders['right'];
      $output .= "\n";

      $this->Output->write($output);
   }

   // ! Column(s)
   private function calculateColumnWidths ()
   {
      $columnWidths = [];

      foreach ($this->headers as $header) {
         $columnWidths[] = mb_strlen($header);
      }

      foreach ($this->rows as $row) {
         foreach ($row as $key => $value) {
            $columnWidths[$key] = max($columnWidths[$key], mb_strlen($value));
         }
      }

      return $columnWidths;
   }

   // @ Border
   private function printHorizontalLine (array $columnWidths, string $position)
   {
      $line = match ($position) {
         'top' => $this->borders['top-left'],
         'mid' => $this->borders['mid-left'],
         'bottom' => $this->borders['bottom-left']
      };

      foreach ($columnWidths as $index => $width) {
         if ($index > 0) {
            $line .= match($position) {
               'top' => $this->borders['top-mid'],
               'mid' => $this->borders['mid-mid'],
               'bottom' => $this->borders['bottom-mid']
            };
         }

         $border = match($position) {
            'top' => $this->borders['top'],
            'mid' => $this->borders['mid'],
            'bottom' => $this->borders['bottom']
         };
         $line .= str_repeat($border, $width + 2);
      }

      $line .= match ($position) {
         'top' => $this->borders['top-right'],
         'mid' => $this->borders['mid-right'],
         'bottom' => $this->borders['bottom-right']
      };
      $line .= "\n";

      $this->Output->write($line);
   }

   public function render ()
   {
      // @ Calculate Column
      // Widths
      $columnWidths = $this->calculateColumnWidths();

      $this->printHorizontalLine($columnWidths, 'top');

      // ! Header
      if (count($this->headers) > 0) {
         $this->printRow($this->headers, $columnWidths);
      }

      // ! Rows (Body)
      if (count($this->rows) > 0) {
         foreach ($this->rows as $index => $row) {
            if ($index === 0)
               $this->printHorizontalLine($columnWidths, 'mid');
   
            $this->printRow($row, $columnWidths);
         }
      }

      // ! Footer
      if (count($this->footers) > 0) {
         $this->printHorizontalLine($columnWidths, 'mid');
         $this->printRow($this->footers, $columnWidths);
      }

      $this->printHorizontalLine($columnWidths, 'bottom');
   }
}
