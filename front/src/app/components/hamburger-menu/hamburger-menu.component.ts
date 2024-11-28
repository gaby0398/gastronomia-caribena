import { Component, HostListener  } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-hamburger-menu',
  standalone: true,
  imports: [CommonModule, MatIconModule, RouterModule], 
  templateUrl: './hamburger-menu.component.html',
  styleUrls: ['./hamburger-menu.component.scss'],
})
export class HamburgerMenuComponent {
  isOpen = false;

 // Abre o cierra el menú al hacer clic en el botón
 toggleMenu() {
  this.isOpen = !this.isOpen;
}

// Escucha clics globales en el documento
@HostListener('document:click', ['$event'])
onClickOutside(event: Event) {
  const targetElement = event.target as HTMLElement;

  // Verifica si el clic ocurrió fuera del menú
  if (!targetElement.closest('.hamburger-container')) {
    this.isOpen = false; // Cierra el menú si está abierto
  }
}
}