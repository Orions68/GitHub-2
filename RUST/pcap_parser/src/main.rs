extern crate nom;
extern crate pcap_parser;
use pcap_parser::*;

use std::fs::File;
use std::io::Read;

fn main() {
	let path = "test.pcap";
	let mut file = File::open(path).unwrap();
	let mut buffer = Vec::new();
	file.read_to_end(&mut buffer).unwrap();
	let mut _num_packets = 0;
	// try pcap first
	match PcapCapture::from_file(&buffer) {
		Ok(capture) => {
			println!("Format: PCAP");
			for _packet in capture.iter_packets() {
				_num_packets += 1;
			}
            println!("Hay Paquetes: {}", _num_packets);
			return;
		},
			_ => ()
	}
	// otherwise try pcapng
	match PcapNGCapture::from_file(&buffer) {
		Ok(capture) => {
			println!("Format: PCAPNG");
			// most pcaps have one section, with one interface
			//
			// global iterator - provides a unified iterator over all
			// sections and interfaces. It will usually work only if there
			// is one section with one interface
			// otherwise, the next iteration code is better
			// for _packet in capture.iter_packets() {
				// num_packets += 1;
			// }
			// The following code iterates all sections, for each section
			// all interfaces, and for each interface all packets.
			// Note that the link type can be different for each interface!
			println!("Num sections: {}", capture.sections.len());
			for (snum, section) in capture.sections.iter().enumerate() {
				println!("Section {}:", snum);
				for (inum, interface) in section.interfaces.iter().enumerate() {
					println!("    Interface {}:", inum);
					println!("        Linktype: {:?}", interface.header.linktype);
					// ...
					for _packet in section.iter_packets() {
						_num_packets += 1;
					}
				}
			}
		},
			_ => ()
	}
}