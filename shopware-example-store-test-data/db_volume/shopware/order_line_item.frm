�
    @  &
         	      �        &  5�  :        //  0                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
  �        �  @ �  @       �#  @ �3  @ A      �C  � �S  � A      	�_ � 
�o � A     �� � A     � � �PRIMARY�fk.order_line_item.order_id�fk.order_line_item.parent_id�product_id�fk.order_line_item.cover_id�fk.order_line_item.promotion_id�                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             ��                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      �      �                         InnoDB      &                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               �                                                                                                                                                                                                                                                               �3  
6        P     k 8)                                          id  version_id  	order_id  order_version_id  
parent_id 	 parent_version_id 
 identifier  referenced_id  product_id  product_version_id  promotion_id  states  label  description  	cover_id  	quantity  unit_price  total_price  type � 
)                                          payload  price_definition  price  
stackable  
removable 	 good 
 	position  custom_fields  created_at  updated_at    @   �?     @   �?  	 #  @   �?   3  @   �?  
 C  �   �?  	 S  �   �?  
D�c   @   �  A�a  �   �   _ �   �?   o �   �?    �   �?   � �  �?  I��  @   �   � H�  ��  	 � �   �?  	 � @      � +��      +��    J��  �   �   � �  �.   � �  �.   � P  �.  
 �       
 �       	 �       
	 �        � �  �.   � �@      � ��     �id�version_id�order_id�order_version_id�parent_id�parent_version_id�identifier�referenced_id�product_id�product_version_id�promotion_id�states�label�description�cover_id�quantity�unit_price�total_price�type�payload�price_definition�price�stackable�removable�good�position�custom_fields�created_at�updated_at� 1  json_unquote(json_extract(`price`,'$.unitPrice'))2  json_unquote(json_extract(`price`,'$.totalPrice'))