import { CommonModule } from '@angular/common';
import { Component, OnInit, OnDestroy, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { RemarkComponent } from '../remark/remark.component';

@Component({
  selector: 'app-index',
  standalone: true,
  imports: [CommonModule, RouterModule, RemarkComponent],
  templateUrl: './index.component.html',
  styleUrls: ['./index.component.css'],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class IndexComponent implements OnInit, OnDestroy {
  images = [
    'assets/images/image1.jpg',
    'assets/images/image2.jpg',
    'assets/images/image3.jpg',
    'assets/images/image4.jpg',
    'assets/images/image5.jpg',
    'assets/images/image6.jpg',
    'assets/images/image7.jpg',
    'assets/images/image8.jpg'
  ];
  currentIndex = 0;
  intervalId: any;

  constructor(private router: Router) { }

  get translateX() {
    return -this.currentIndex * 100;
  }

  ngOnInit() {
    // Iniciar el ciclo automático
    this.intervalId = setInterval(() => {
      this.next();
    }, 3000); // Cambia cada 3 segundos
  }

  ngOnDestroy() {
    // Limpiar el intervalo cuando el componente se destruya
    clearInterval(this.intervalId);
  }

  next() {
    this.currentIndex = (this.currentIndex + 1) % this.images.length;
  }

  prev() {
    this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
  }

  navigateToInfo() {
    this.router.navigate(['/informacion']);
  }

  navigateToLogin() {
    this.router.navigate(['/login']);
  }
}